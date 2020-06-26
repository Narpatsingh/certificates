<?php

namespace Sushil\Certificate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sushil\Certificate\Models\Event;
use Sushil\Certificate\Models\Certificate;
use Sushil\Certificate\Models\Account;
use DB;

class ImportCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event_id;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($event_id,$type = 'override')
    {
        $this->event_id = $event_id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event_id = $this->event_id;
        $certificates = Certificate::where(array('event_id'=>$event_id))->get();
        $event = Event::findOrFail($event_id);
        $googleClient = Account::getGoogleClient($event->google_account_id);
        $driveService = new \Google_Service_Drive($googleClient);
        //Delete all the previous certificates 
        if($certificates->count()){
            if($this->type == 'override'){
                foreach ($certificates as $key => $certi) {
                    //Delete slide and pdf from the drive
                    if(!empty($certi->google_certificate_pdf_id) && !empty($certi->google_certificate_slide_id)){
                        try{
                            $driveService->files->delete($certi->google_certificate_pdf_id);
                            $driveService->files->delete($certi->google_certificate_slide_id);
                        }catch (\Exception $ex) {
                            
                        }
                        \File::deleteDirectory(public_path('event_certificates/'.$event_id.'/'.$certi->certificate_pdf_file));
                    }
                    $certi->delete();
                }
            }
        }
        try{
            // GET SHEETS based on id
            $sheetsService = new \Google_Service_Sheets($googleClient);
            $dataSpreadsheetId = $event->google_sheet_id;
            // Use the Sheets API to load data, one record per row.
            $spreadSheet = $sheetsService->spreadsheets->get($dataSpreadsheetId);
            $sheets = $spreadSheet->getSheets();
            $titleSheet = !empty($sheets[0]['properties']['title'])?$sheets[0]['properties']['title']:'';
            $dataRangeNotation = $titleSheet.'!A1:F10';
            $sheetsResponse =  $sheetsService->spreadsheets_values->get($dataSpreadsheetId, $dataRangeNotation);

            $values = $sheetsResponse['values'];
            $column = $values[0];
            unset($values[0]);
            foreach ($values as $value) {
                $email = $value[2];
                $payload = [];
                foreach ($column as $key => $col) {
                    $payload[$col] = !empty($value[$key])?$value[$key]:'';    
                }
                if($this->type == 'override'){
                    $certificate = new Certificate;
                    $certificate->event_id = $event_id;
                    $certificate->payload = json_encode($payload);
                    $certificate->email = $payload['email'];
                    $certificate->save();
                }else{
                    $certificate =  Certificate::where(array('event_id'=>$event_id,'email'=>$payload['email']))->count();
                    if(!$certificate){
                        $certificate = new Certificate;
                        $certificate->event_id = $event_id;
                        $certificate->payload = json_encode($payload);
                        $certificate->email = $payload['email'];
                        $certificate->save();
                    }
                }
            }

            if($event) {
                $event->status = 'Imported';
                $event->event_error = null;
                $event->save();
            }
        }catch (\Exception $ex) {
            $class = get_class($ex);
            $error_message = $ex->getMessage();
            if($class == 'Google_Service_Exception'){
                $message = json_decode($error_message,1);
                $error_message = $message['error']['message'];
            }
            if($event) {
                $event->status = 'Importing Failed';
                $event->event_error = $error_message; 
                $event->save();
            }
        }
    }
}
