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
use File;


class GenerateCertificateJob implements ShouldQueue
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
        $event = Event::findOrFail($event_id);
        $certificates = Certificate::where(array('event_id'=>$event_id))->get();
        if($certificates->count()){
            if($this->type == 'override'){
                foreach ($certificates as $key => $certi) {
                    if(!empty($certi->google_certificate_pdf_id)){
                        //Delete slide and pdf from the drive
                        try{
                            $googleClient = Account::getGoogleClient($event->google_account_id);
                            $driveService = new \Google_Service_Drive($googleClient);
                            $driveService->files->delete($certi->google_certificate_pdf_id);
                        }catch (\Exception $ex) {
                            
                        }
                        File::deleteDirectory(public_path('event_certificates/'.$event_id.'/'.$certi->certificate_pdf_file));
                    }
                }
            }
            try{
                $googleClient = Account::getGoogleClient($event->google_account_id);
                $driveService = new \Google_Service_Drive($googleClient);
                $slidesService = new \Google_Service_Slides($googleClient);
                $presentationId = $event->google_slide_id;
                $parentid = $event->google_upload_folder;
                foreach ($certificates as $certificate) {
                    if(empty($certificate->certificate_pdf_file) || $this->type == 'override'){
                        $copy = new \Google_Service_Drive_DriveFile(array(
                            'name' => $certificate->email . '_certificate',
                            'parents' => array($parentid)
                        ));

                        //Set the Parent Folder to existing_folder
                        $driveResponse = $driveService->files->copy($presentationId, $copy);
                        $presentationCopyId = $driveResponse->id;
                        
                        //Create the text merge (replaceAllText) requests for this presentation.
                        $requests = array();
                        $mappedFields = $event->email_recipient_field;
                        $payLoad = json_decode($certificate->payload,1);
                        $mappedFields = json_decode($mappedFields,true);

                        foreach($mappedFields as $field=>$column){
														if(!is_numeric($field)){
															$requests[] = new \Google_Service_Slides_Request(array(
																			'replaceAllText' => array(
																							'containsText' => array(
																											'text' => $field,
																											'matchCase' => true
																							),
																							'replaceText' => isset($payLoad[$column])?$payLoad[$column]:""
																			)
															));
														}
                        }

                        //Execute the requests for this presentation.
                        $batchUpdateRequest = new \Google_Service_Slides_BatchUpdatePresentationRequest(array(
                            'requests' => $requests
                        ));
                        $response = $slidesService->presentations->batchUpdate($presentationCopyId, $batchUpdateRequest);

                        $file = $driveService->files->export($presentationCopyId, "application/pdf",array( 'alt' => 'media' ))->getBody()->getContents();
                        
                        $path = public_path('event_certificates/'.$event_id.'/');
                        if(!File::isDirectory($path)){
                            File::makeDirectory($path, 0777, true, true);
                        }

                        file_put_contents(public_path('event_certificates/'.$event_id.'/'.$certificate->email . '_certificate.pdf'), $file);
                        
                        $drive_file = new \Google_Service_Drive_DriveFile(array(
                            'name' => $certificate->email . '_certificate',
                            'mimeType' => "application/pdf",
                            'writersCanShare' => true,
                            'parents' => array($parentid)
                        ));

                        
                        $createdFile = $driveService->files->create($drive_file, [
                            'data' => $file,
                            'mimeType' => '"application/pdf"',
                            'uploadType' => 'multipart',
                        ]);
                        $pdf_file_id = $createdFile->id;
                        
                        //Give access permission to generated file

                        $Permission = new \Google_Service_Drive_Permission(array(
                            'type' => 'anyone',
                            'role' => "reader",
                            'additionalRoles' => [],
                            'withLink' => true,
                        ));
                        $drive_permission = $driveService->permissions->create(
                            $pdf_file_id, $Permission);

                        $driveService->files->delete($presentationCopyId);

                        //Save the required detail in certificate tabel
                        $certificate_data = Certificate::findOrFail($certificate->id);
                        $certificate_data->google_certificate_pdf_id = $pdf_file_id;
                        //$certificate_data->google_certificate_slide_id = $presentationCopyId;
                        $certificate_data->google_certificate_pdf_link = 'https://drive.google.com/file/d/'.$pdf_file_id.'/view';
                        $certificate_data->certificate_pdf_file =  $certificate->email . '_certificate.pdf';
                        $certificate_data->save();
                    }
                }
                if($event) {
                    $event->status = 'Generated';
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
                    $event->status = 'Generating Failed';
                    $event->event_error = $error_message; 
                    $event->save();
                }
            }
        }
    }
}
