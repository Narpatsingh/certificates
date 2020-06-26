<?php

namespace Sushil\Certificate\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = "certificates";

    protected $fillable = ['event_id','payload','name','email','certificate_url','google_certificate_pdf_id','google_certificate_slide_id','google_certificate_pdf_link','certificate_pdf_file','mail_send'];

    protected function event(){
        return $this->belongsTo('Sushil\Certificate\Models\Event','event_id','id');
    }
}
