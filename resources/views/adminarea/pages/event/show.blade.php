@extends('layouts.app')

@section('title', __('Event Detail'))
	
@section('content')
<style type="text/css">
	.tdLabel{
		width: 30%
	}
</style>
<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title">
	
			{{__('Event Detail')}}
				
		</h3>
		<a class="btn btn-default btn-sm pull-right" href="{{ route('events.index') }}" style="margin-left: 1%">{{__('Back')}}</a>

		<a class="btn btn-default btn-sm pull-right" href="{{ route('certificates.index',$event->id) }}">{{__('Go To Certificates')}}</a> 
		
	</div>
	@if(!empty($event->event_error))						
	<div class="alert alert-danger">
	    {{ $event->event_error }}
    </div>
    @endif

	<!-- {{ Form::model($event, [ 'novalidate'=>true,'id' => 'frm_events', 'files' => true]) }}
	 -->
	<div class="box-body">
		<h5 class="border-bottom pt-4 mb-3 text-primary">{{__('Event Information')}}</h5>
		<div class="row">
			<table class="table table-bordered table-striped">
				<tr>
					<td class="tdLabel" width="200px"><strong>{{__('Event Name')}}</strong></td>
					<td><span>{{ $event->name }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Event Website')}}</strong></td>
					<td><span>{{ $event->website }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Event description')}}</strong></td>
					<td><span>{{ $event->description }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Organization Name')}}</strong></td>
					<td><span>{{ $event->org_name }}</span></td>
				</tr>
					<tr>
					<td class="tdLabel"><strong>{{__('Organization Website')}}</strong></td>
					<td><span>{{ $event->org_website }}</span></td>
				</tr>

			</table>
				

		</div>
		<h5 class="border-bottom pt-4 mb-3 text-primary">{{__('Certificate Content')}}</h5>
		<div class="row">
			<table class="table table-bordered table-striped">
				<tr>
					<td class="tdLabel" width="200px"><strong>{{__('Google Account')}}</strong></td>
					<td><span>{{ $accounts->name }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Certificate Google Slide')}}</strong></td>
					<td><span>{{ $event->google_slide_id_name }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Attendees Google Sheet')}}</strong></td>
					<td><span>{{ $event->google_sheet_id_name }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Certificates Google Folder')}}</strong></td>
					<td><span>{{ $event->google_upload_folder_name }}</span></td>
				</tr>
					

			</table>
		</div>
		<h5 class="border-bottom pt-4 mb-3 text-primary">{{__('Certificate Email Content')}}</h5>
		<div class="row">
			<table class="table table-bordered table-striped">
				<tr>
					<td class="tdLabel" width="200px"><strong>{{__('Email Subject')}}</strong></td>
					<td><span>{{ $event->email_subject }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Email Sender Reply To')}}</strong></td>
					<td><span>{{ $event->email_sender_replyto }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Email Message')}}</strong></td>
					<td><p>{{!! $event->email_message !!}}</p></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Email Sender Name')}}</strong></td>
					<td><span>{{ $event->email_sender_name }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Email Sender Email')}}</strong></td>
					<td><span>{{ $event->email_sender_email }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Email Recipient CC')}}</strong></td>
					<td><span>{{ $event->email_recipient_cc }}</span></td>
				</tr>
				<tr>
					<td class="tdLabel"><strong>{{__('Email Recipient BCC')}}</strong></td>
					<td><span>{{ $event->email_recipient_bcc }}</span></td>
				</tr>				
			</table>
		</div>		
		<h5 class="border-bottom pt-4 mb-3 text-primary">{{__('Settings')}}</h5>
		<div class="row">
			<table class="table table-bordered table-striped">
				<tr>
					<td class="tdLabel" width="200px"><strong>{{__('Attach Certificate')}}</strong></td>
					<td><span>{{ $event->attach_certificate }}</span></td>
				</tr>
			</table>
		</div>

		<h5 class="border-bottom pt-4 mb-3 text-primary">{{__('Status')}}</h5>
		<div class="row">
			<table class="table table-bordered table-striped">
				<tr>
					<td class="tdLabel" width="200px"><strong>{{__('Certificate Status')}}</strong></td>
					<td><span>{{ $event->status }}</span></td>
				</tr>
				
			</table>
		 </div>
	
		
	</div>

</div>
@endsection

