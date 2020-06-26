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


class SingleGenerateCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $certificate_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($certificate_id)
    {
        $this->certificate_id = $certificate_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $certificate_id = $this->certificate_id;
        $certificate = Certificate::findOrFail($certificate_id);
        $event = Event::findOrFail($certificate->event_id);
        
        if(!empty($certificate->google_certificate_pdf_id)){
            //Delete slide and pdf from the drive
            try{
                $googleClient = Account::getGoogleClient($event->google_account_id);
                $driveService = new \Google_Service_Drive($googleClient);
                $driveService->files->delete($certificate->google_certificate_pdf_id);
            }catch (\Exception $ex) {
                $response = $ex->getMessage();
            }
            File::deleteDirectory(public_path('event_certificates/'.$certificate->event_id.'/'.$certificate->certificate_pdf_file));
        
        }
        try{
            $googleClient = Account::getGoogleClient($event->google_account_id);
            $driveService = new \Google_Service_Drive($googleClient);
            $slidesService = new \Google_Service_Slides($googleClient);
            $presentationId = $event->google_slide_id;
            $parentid = $event->google_upload_folder;
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

            //Execute the requests for this presentation.
            $batchUpdateRequest = new \Google_Service_Slides_BatchUpdatePresentationRequest(array(
                'requests' => $requests
            ));
            $response = $slidesService->presentations->batchUpdate($presentationCopyId, $batchUpdateRequest);

            $file = $driveService->files->export($presentationCopyId, "application/pdf",array( 'alt' => 'media' ))->getBody()->getContents();
            
            $path = public_path('event_certificates/'.$event->id.'/');
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }

            file_put_contents(public_path('event_certificates/'.$event->id.'/'.$certificate->email . '_certificate.pdf'), $file);
            
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
            $certificate->google_certificate_pdf_id = $pdf_file_id;
            $certificate->google_certificate_slide_id = $presentationCopyId;
            $certificate->google_certificate_pdf_link = 'https://drive.google.com/file/d/'.$pdf_file_id.'/view';
            $certificate->certificate_pdf_file =  $certificate->email . '_certificate.pdf';
            $certificate->save();
            
        }catch (\Exception $ex) {
            $response = $ex->getMessage();
        }
    }
}
