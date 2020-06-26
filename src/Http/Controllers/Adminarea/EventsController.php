<?php

namespace Sushil\Certificate\Http\Controllers\Adminarea;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Sushil\Certificate\Models\Event;
use Sushil\Certificate\Models\Certificate;
use Sushil\Certificate\Models\Account;
use Sushil\Certificate\Http\Jobs\ImportCertificateJob;
use Sushil\Certificate\Http\Jobs\GenerateCertificateJob;
use Sushil\Certificate\Http\Jobs\SendingCertificateJob;
use Sushil\Certificate\Http\Jobs\SingleGenerateCertificateJob;
use Sushil\Certificate\Http\Jobs\SingleSendingCertificateJob;
use Sushil\Certificate\DataTables\Adminarea\AdminsDataTable;
use Cortex\Foundation\Http\Controllers\AuthenticatedController;
use Validator;
use Session;

class EventsController extends AuthenticatedController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdminsDataTable $adminsDataTable)
    {
        $request = new Request ;
        $conditions[] = array('user_id', auth()->user()->id);
        $events = Event::select('id','name','website','org_name','org_website','status','scheduled_at','created_at')
                                        ->where($conditions)->get();
        if ($request->ajax()) {
            print_r($events);exit;
			return Datatables::of($events)->make(true);
		}
				
        return $adminsDataTable->with([
            'id' => 'adminarea-members-index-table',
            'events'=>$events,
        ])->render('sushil/makegui::adminarea.pages.event.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AdminsDataTable $adminsDataTable)
    {
        $request = new Request;
        // $userData = Member::findOrFail(auth()->user()->id);
        // if($userData->email_setting == '' || $userData->email_setting == null){

        //     Session::flash('warning',  __('Please add your email setting and enable any one email setting from profile before create a Event.'));
        //     return redirect(route('events.index'));
        // }
        $event = [];
		$accounts = Account::where('user_id', auth()->user()->id)->pluck('name', 'id');
        return view('sushil/makegui::adminarea.pages.event.create',compact('event','accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate_event = Validator::make($request->all(), Event::rules());
        if($validate_event->fails()){
            $errors = $validate_event->messages();
            return redirect()->back()->withErrors($errors)->withInput();
        } 
        else{
            $event = new Event;
            $event->user_id = auth()->user()->id;
            $event->name = $request->name;
            $event->website = $request->website;
            $event->description = $request->description;
            $event->org_name = $request->org_name;
            $event->org_website = $request->org_website;

            $event->google_account_id = $request->google_account_id;
            $event->google_slide_id = $request->google_slide_id;
            $event->google_sheet_id = $request->google_sheet_id;
            $event->google_upload_folder = $request->google_upload_folder;
            $event->email_recipient_field = $request->email_recipient_field;

            $event->google_slide_id_name = $request->google_slide_id_name;
            $event->google_sheet_id_name = $request->google_sheet_id_name;
            $event->google_upload_folder_name = $request->google_upload_folder_name;
            $event->email_recipient_field = $request->email_recipient_field;

            $event->email_subject = $request->email_subject;
            $event->email_message = $request->email_message;
            $event->email_recipient_cc = $request->email_recipient_cc;
            $event->email_recipient_bcc = $request->email_recipient_bcc;
            $event->email_sender_name = $request->email_sender_name;
            $event->email_sender_email = $request->email_sender_email;
            $event->email_sender_replyto = $request->email_sender_replyto;
						
            $event->attach_certificate = $request->attach_certificate;
            //$event->public_link = $request->public_link;
            $event->scheduled_at = $request->scheduled_at;
            $event->save();
            Session::flash('success',  __('Event added successfully.'));
            return redirect(route('events.index'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);
        $email_recipient_field= json_decode($event->email_recipient_field,true);
        $accounts = Account::findOrFail($event->google_account_id);
        return view('Event.show',compact('event','id','accounts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        // $userData=User::findOrFail(auth()->user()->id);
        // if($userData->email_setting == '' || $userData->email_setting == null){

        //     Session::flash('warning',  __('Please add your email setting and enable any one email setting from profile before create a Event.'));
        //     return redirect(route('events.index'));
        // }

        $event = Event::findOrFail($id);
        $email_recipient_field= json_decode($event->email_recipient_field,true);
        $arrData = array(
        'slide' => array_keys($email_recipient_field),
        'sheet' => array_values($email_recipient_field)
        );

        $data = json_encode($arrData);

        $accounts = Account::where('user_id', auth()->user()->id)->pluck('name', 'id');

        return view('sushil/makegui::adminarea.pages.event.create',compact('event','id','accounts','data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $validate_event = Validator::make($request->all(), Event::rules($request->id));
        
        if($validate_event->fails()){
            $errors = $validate_event->messages();
            return redirect()->back()->withErrors($errors)->withInput();
        }
        else{
            $event->update($request->all());
        }    
        Session::flash('success',__('Event updated successfull'));
        return redirect(route('events.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect(route('events.index'));
    }

    public function import_certificate($id,$type = 'override')
    {
        $event = Event::findOrFail($id);
        if($event) {
            $event->status = 'Importing';
            $event->save();
            ImportCertificateJob::dispatch($id,$type);
            //ImportCertificateJob::dispatch($id)->delay(now()->addMinutes(1));
        }else{
            Session::flash('warning',__('Event not found.'));
        }    
        return redirect(route('events.index'));
        
    }

    public function generate_certificate($id,$type = 'override')
    {
        $event = Event::findOrFail($id);
        if($event) {
            $event->status = 'Generating';
            $event->save();
            GenerateCertificateJob::dispatch($id,$type);
            //GenerateCertificateJob::dispatch($id)->delay(now()->addMinutes(1));
        }else{
            Session::flash('warning',__('Event not found.'));
        }    
        return redirect(route('events.index'));
        
    }

    public function send_certificate($id,$type = 'override')
    {
        $event = Event::findOrFail($id);
        if($event) {
            $event->status = 'Sending';
            $event->save();
            SendingCertificateJob::dispatch($event,$type);
            //SendingCertificateJob::dispatch($id)->delay(now()->addMinutes(1));
        }else{
            Session::flash('warning',__('Event not found.'));
        }    
        return redirect(route('events.index'));
        
    }

    public function single_generate_certificate($id)
    {
        $Certificate = Certificate::findOrFail($id);
        if($Certificate) {
            SingleGenerateCertificateJob::dispatch($id);
            //SingleGenerateCertificateJob::dispatch($id)->delay(now()->addMinutes(1));
        }else{
            Session::flash('warning',__('Certificate not found.'));
        }    
        return redirect()->back();
        
    }

    public function single_send_certificate($id)
    {
        $Certificate = Certificate::findOrFail($id);
        if($Certificate) {
            SingleSendingCertificateJob::dispatch($id);
            //SingleSendingCertificateJob::dispatch($id)->delay(now()->addMinutes(1));
        }else{
            Session::flash('warning',__('Certificate not found.'));
        }    
        return redirect()->back();
        
    }
}
