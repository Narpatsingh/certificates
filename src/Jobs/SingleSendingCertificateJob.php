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
use Sushil\Certificate\Models\User;
use Mail;
use App\Mail\GeneralMail;
use Config;



class SingleSendingCertificateJob implements ShouldQueue
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
        $user_detail = User::findOrFail($event->user_id);
        
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
                $certificate->save();    
            }catch (\Exception $ex) {
                $certificate->mail_send = 0;
                $certificate->save();
            }
        }else{
            $certificate->mail_send = 0;
            $certificate->save();
        }
    }
}
