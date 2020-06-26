<?php

namespace Sushil\Certificate\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sushil\Certificate\Models\Event;
use URL;

class GeneralMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event_deteails,$certificate_detail = '')
    {
        $this->event_deteails = $event_deteails;
        $this->certificate_detail = $certificate_detail;
        $this->user_email = $certificate_detail->email;
        $this->file_path = $certificate_detail->certificate_pdf_file;
        $this->subject = $this->replaceContent($event_deteails,$event_deteails->email_subject);
        $this->email_body = $this->replaceContent($event_deteails,$event_deteails->email_message);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail_build_data = $this->subject($this->subject)
                    ->html($this->email_body, 'text/html');
        if($this->event_deteails->attach_certificate == 'Yes'){
            $mail_build_data->attach(public_path('/event_certificates/'.$this->event_deteails->id.'/'.$this->file_path), [
                         'as' => $this->user_email.'.pdf',
                        'mime' => 'application/pdf',
                    ]);
        }
        return $mail_build_data;
    }

    public function replaceContent($data,$fieldData)
    {
        $replacement = array(
            "[[name]]" => isset($this->user_email) ? $this->user_email : '',
            "[[event_name]]" => isset($data->name) ? $data->name : '',
            "[[event_website]]" => isset($data->website) ? $data->website : '',
            "[[event_description]]" => isset($data->description) ? $data->description : '',
            "[[event_org_name]]" => isset($data->org_name) ? $data->org_name : '',
            "[[event_org_website]]" => isset($data->org_website) ? $data->org_website : '',
            "[[certificate_gdrive_link]]" => isset($this->certificate_detail->google_certificate_pdf_link) ? $this->certificate_detail->google_certificate_pdf_link : '',
            "[[certificate_web_link]]" => isset($this->certificate_detail->google_certificate_pdf_link) ? URL::to('/event_certificates').'/'.$data->id.'/'.$this->file_path : '',
        );
				$payload = json_decode($this->certificate_detail->payload,true);
				foreach($payload as $field => $value){
					$replacement['[['.$field.']]'] = $value;
				}
        $body = str_replace(array_keys($replacement), array_values($replacement),$fieldData);
				return $body;
    }
}
