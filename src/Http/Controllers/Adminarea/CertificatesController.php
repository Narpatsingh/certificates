<?php

namespace Sushil\Certificate\Http\Controllers\Adminarea;

use Sushil\Certificate\Models\Certificate;
use Sushil\Certificate\Models\Event;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;;


class CertificatesController extends Controller
{
   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($eventId,Request $request)
    {		
				if ($request->ajax()) {										
						$certificates = Certificate::select('name','email','payload','certificate_url','created_at','id','certificate_pdf_file','event_id')
															->where('event_id',$eventId);
						return Datatables::of($certificates)->make(true);
				}
				$event = Event::findOrFail($eventId, array('name'));
				return view('Certificate.index')->with(compact('eventId','event'));
    }
}
