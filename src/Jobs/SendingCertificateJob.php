<?php

namespace Sushil\Certificate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sushil\Certificate\Models\Event;
use Sushil\Certificate\Models\Certificate;
use Sushil\Certificate\Models\EmailSetting;
use Sushil\Certificate\Models\User;
use Sushil\Certificate\Models\Account;
use Mail;
use Sushil\Certificate\\Mail\GeneralMail;
use Config;



class SendingCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event_detail;
    protected $type;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($event_detail,$type = 'override')
    {
        $this->event_detail = $event_detail;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = $this->event_detail;
        $certificates = Certificate::where(array('event_id'=>$event->id))->get();
        $user_detail = User::findOrFail($event->user_id);
        if($certificates->count()){
            $email_setting = User::configEmailSetting($event->user_id);
            if($email_setting){
                if($event->email_sender_email){
                    Config::set('mail.from.address', $event->email_sender_email);
                }elseif($user_detail->email_sender_email){
                    Config::set('mail.from.address', $user_detail->email_sender_email);
                }

                if($event->email_sender_name){
                    Config::set('mail.from.name', $event->email_sender_name);
                }elseif($user_detail->email_sender_name){
                    Config::set('mail.from.name', $user_detail->email_sender_name);
                }
                $email_recipient_cc = '';
                if(!empty($event->email_recipient_cc)){
                    $email_recipient_cc = explode(',', $event->email_recipient_cc);
                }elseif(!empty($user_detail->email_cc)){
                    $email_recipient_cc = explode(',', $user_detail->email_cc);
                }
                $email_recipient_bcc = '';
                if(!empty($event->email_recipient_bcc)){
                    $email_recipient_bcc = explode(',', $event->email_recipient_bcc);
                }elseif(!empty($user_detail->email_bcc)){
                    $email_recipient_bcc = explode(',', $user_detail->email_bcc);
                }
                $email_sender_replyto = '';
                if(!empty($event->email_sender_replyto)){
                    $email_sender_replyto = $event->email_sender_replyto;
                }elseif(!empty($user_detail->email_sender_replyto)){
                    $email_sender_replyto = $user_detail->email_sender_replyto;
                }
                $error_message = '';
								$dataValues = array();
                foreach ($certificates as $key => $certificate) {
                    if(empty($certificate->mail_send) || $this->type == 'override'){
                        try{
                            $mail_data = new GeneralMail($event,$certificate);
                            if(!empty($email_sender_replyto)){
                                $mail_data->replyTo($email_sender_replyto);
                            }
                            if(!empty($email_recipient_cc)){
                                $mail_data->cc($email_recipient_cc);
                            }
                            if(!empty($email_recipient_bcc)){
                                $mail_data->bcc($email_recipient_bcc);
                            }
                            Mail::to($certificate->email)->send($mail_data);
                            if(!Mail::failures()){
                                $certificate->mail_send = empty($certificate->mail_send)?1:2;
                            }else{
                                $certificate->mail_send = 0;
                            }
														//Create Payload for Result Sheet
														$payLoad = json_decode($certificate->payload,1);
														$payLoad['certificate_link'] = $certificate->google_certificate_pdf_link;
														$payLoad['emai_sent_at'] = date("Y-m-d H:i:s");
														$certificate->payload = json_encode($payLoad);
                            $certificate->save();
														
														if(empty($dataValues)){
															$dataValues[] = array_keys($payLoad);
														}
														$dataValues[] = array_values($payLoad);														

                        }catch (\Exception $ex) {
                            $error_message = $ex->getMessage();
                            $event->status = 'Sending Failed';
                            $event->event_error = $error_message;
                            $event->save();
                        }
                    }
                }
                if(empty($error_message)){
                    $event->status = 'Completed';
                    $event->event_error = null;
                    $event->save();
                }
								//Update Sheet Value
								$googleClient = Account::getGoogleClient($event->google_account_id);
								$this->updateResultSheet($googleClient,$event->google_sheet_id,$dataValues);
            }else{
                $event->status = 'Sending Failed';
                $event->event_error = 'Email setting not selected from profile.';
                $event->save();
            }
        }
    }
		function updateResultSheet($googleClient,$spreadsheetId,$dataValues){
			try{
				$service = new \Google_Service_Sheets($googleClient);
				//Get Old Sheet name
				$spreadSheet = $service->spreadsheets->get($spreadsheetId);
				$sheets = $spreadSheet->getSheets();
				$resultSheet=$sheets[0]['properties']['title']."_Result";
				$isSheetExist = false;
				foreach($sheets as $sheet){
					if($resultSheet == $sheet['properties']['title']){
						$isSheetExist = true;
					}
				}

				//Create New Sheet to Insert Updated Field
				if(!$isSheetExist){
					$body = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
									'requests' => array(
											'addSheet' => array(
													'properties' => array(
															'title' => $resultSheet
													)
											)
									)
							));
					$result = $service->spreadsheets->batchUpdate($spreadsheetId,$body);
				}else{
					// TODO: Assign values to desired properties of `requestBody`:
					$requestBody = new \Google_Service_Sheets_ClearValuesRequest();
					$response = $service->spreadsheets_values->clear($spreadsheetId, $resultSheet, $requestBody);
				}
				
				$data[] = new \Google_Service_Sheets_ValueRange([
						'range' => $resultSheet,
						'values' => $dataValues
				]);

				// Additional ranges to update ...
				$body = new \Google_Service_Sheets_BatchUpdateValuesRequest([
						'valueInputOption' => "RAW",
						'data' => $data
				]);
				$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);	
			}catch (\Exception $ex) {
					echo $ex->getMessage();
			}	
		}
}
