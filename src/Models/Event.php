<?php


namespace Sushil\Certificate\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = "events";

    protected $fillable = ["google_account_id","google_slide_id","google_sheet_id","user_id","google_upload_folder","email_subject","email_message","email_recipient_field","email_recipient_cc","email_recipient_bcc","email_sender_name","email_sender_email","email_sender_replyto","public_link","attach_certificate","status","event_error"];

    public static function rules ($id=null){
        return [
        	"name" => 'required',
			"google_account_id" => 'required',
        	"google_slide_id" => 'required',
        	"google_sheet_id" => 'required',
        	"google_upload_folder" => 'required',
        	"email_recipient_field" => 'required',
        	"attach_certificate" => 'required',
            "email_subject"=>'required',
            "email_message"=>'required'
        ];
    }


    public function user(){
        return $this->belongsTo('Sushil\Certificate\Models\User','user_id','id');
    }
}
