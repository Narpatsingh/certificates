{{-- Master Layout --}}
@extends('cortex/foundation::adminarea.layouts.default')

{{-- Page Title --}}

@section('title')
    {{ config('app.name').' » Create New Event' }}
@endsection


{{-- Main Content --}}
@section('content')
<div class="content-wrapper">
        <section class="content-header">
            <h1>
                <ol class="breadcrumb">
                    <li><a href="{{route('adminarea.home')}}"><i class="fa fa-dashboard"></i>Cortex</a></li>
    	            <li><a href="http://cortex.local/adminarea/members">Events</a></li>
				    <li class="active">Create New Event</li>
	            </ol>
            </h1>
        </section>

        {{-- Main content --}}
        <section class="content">

            <div class="nav-tabs-custom">						
			@if(empty($event))
			{{ Form::open(['method' => 'POST' ,'route' => ['adminarea.certificates.events.store'], 'id' => 'frm_events' ,'novalidate'=>true,'class'=>'multiple-save','files'=>true]) }}
			@else
			{{ Form::model($event, ['method' => 'PUT' ,'route' => ['adminarea.certificates.events.update', $event->id] , 'novalidate'=>true,'id' => 'frm_events', 'files' => true]) }}
			@endif
			{{ csrf_field() }}
			<div class="box-body">
				<h4 class="border-bottom pt-4 mb-3 text-primary">{{__('Event Information')}}</h4>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} ">

							{{ Form::label('name', __('Event Name'),array('class'=>'required')) }}<span class="after"></span>
							{{ Form::text('name', null, array('required','id' =>"name",'placeholder' => __('Enter event name'),'class' => 'form-control')) }}

							@if ($errors->has('name'))
							<span class="help-block">
									<strong>{{ $errors->first('name') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('website') ? ' has-error' : '' }} ">

							{{ Form::label('website', __('Event Website')) }}<span class="after"></span>
							{{ Form::text('website', null, array('id' =>"website",'placeholder' => __('Enter event website'),'class' => 'form-control')) }}

							@if ($errors->has('website'))
							<span class="help-block">
									<strong>{{ $errors->first('website') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
							<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }} ">

								{{ Form::label('description', __('Event description')) }}<span class="after"></span>
								{{ Form::textarea('description', null, array('id' =>"description",'rows'=>2,'placeholder' => __('Enter Event description'),'class' => 'form-control')) }}

								@if ($errors->has('description'))
								<span class="help-block">
										<strong>{{ $errors->first('description') }}</strong>
								</span>
								@endif
							</div>
						</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('org_name') ? ' has-error' : '' }} ">

							{{ Form::label('org_name', __('Organization Name')) }}<span class="after"></span>
							{{ Form::text('org_name', null, array('id' =>"org_name",'placeholder' => __('Enter organization name'),'class' => 'form-control')) }}

							@if ($errors->has('org_name'))
							<span class="help-block">
									<strong>{{ $errors->first('org_name') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('org_website') ? ' has-error' : '' }} ">

							{{ Form::label('org_website', __('Organization Website')) }}<span class="after"></span>
							{{ Form::text('org_website', null, array('id' =>"org_website",'placeholder' => __('Enter Organization website'),'class' => 'form-control')) }}

							@if ($errors->has('org_website'))
							<span class="help-block">
									<strong>{{ $errors->first('org_website') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<h4 class="border-bottom pt-4 mb-3 text-primary">{{__('Certificate Content')}}</h4>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group{{ $errors->has('google_account_id') ? ' has-error' : '' }} ">
							{{ Form::label('google_account_id', __('Google Account'),array('class'=>'required')) }}<span class="after"></span>
							{{ Form::select('google_account_id', $accounts,null, array('id' =>"google_account_id", 'placeholder' => __('Please select Google Account'),'class' => 'form-control','onchange'=>'onGoogleAccountChange()')) }}

							@if ($errors->has('google_account_id'))
							<span class="help-block">
									<strong>{{ $errors->first('google_account_id') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-8">
						<div class="form-group {{ $errors->has('google_slide_id') || $errors->has('google_sheet_id') || $errors->has('google_upload_folder') ? ' has-error' : '' }} ">
							<div style="margin-top: 30px"></div>
							<div class="row">
								<div class="col-md-4">
								   <a href="javascript:void(0);" class="btn btn-primary  btn-block @if(!empty($event)) {{ ' btn-success'}} @endif" onclick="googlePicker('google_slide_id')"><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i> Certificate Google Slide</a>
									<div id="google_slide_id_label">
										@if(!empty($event))
											<b>Selected document: </b> {{ $event->google_slide_id_name }}
										@endif
									</div>
									@if ($errors->has('google_slide_id'))
									<div class="help-block">
											<strong>{{ $errors->first('google_slide_id') }}</strong>
									</div>
									@endif
								</div>
								<div class="col-md-4">
									<a href="javascript:void(0);" class="btn btn-primary btn-block @if(!empty($event)) {{ ' btn-success'}} @endif" onclick="googlePicker('google_sheet_id')"><i class="fa fa-file-excel-o"></i> Attendees Google Sheet</a>
									<div id="google_sheet_id_label">
										@if(!empty($event))
											<b>Selected document: </b> {{ $event->google_sheet_id_name }}
										@endif
									</div>
									@if ($errors->has('google_sheet_id'))
									<div class="help-block">
											<strong>{{ $errors->first('google_sheet_id') }}</strong>
									</div>
									@endif
								</div>
								<div class="col-md-4">
									<a href="javascript:void(0);" class="btn btn-primary  btn-block @if(!empty($event)) {{ ' btn-success'}} @endif" onclick="googlePicker('google_upload_folder')"><i class="fa fa-folder" aria-hidden="true"></i> Certificates Google Folder</a>
									<div id="google_upload_folder_label">
										@if(!empty($event))
											<b>Selected folder: </b> {{ $event->google_upload_folder_name }}
										@endif
									</div>
									@if ($errors->has('google_upload_folder'))
									<div class="help-block">
											<strong>{{ $errors->first('google_upload_folder') }}</strong>
									</div>
									@endif
								</div>
							</div>
							{{ Form::hidden('google_slide_id', null, array('id' =>"google_slide_id")) }}
							{{ Form::hidden('google_slide_id_name', null, array('id' =>"google_slide_id_name")) }}
							{{ Form::hidden('google_sheet_id', null, array('id' =>"google_sheet_id")) }}
							{{ Form::hidden('google_sheet_id_name', null, array('id' =>"google_sheet_id_name")) }}
							{{ Form::hidden('google_upload_folder', null, array('id' =>"google_upload_folder")) }}
							{{ Form::hidden('google_upload_folder_name', null, array('id' =>"google_upload_folder_name")) }}
							{{ Form::hidden('email_recipient_field', null, array('id' =>"email_recipient_field")) }}
						</div>
					</div>
				</div>
				<h4 class="border-bottom pt-4 mb-3 text-primary">{{__('Certificate Email Content')}}</h4>
				<div class="row">
					<div class="col-md-8">
						<div class="form-group{{ $errors->has('email_subject') ? ' has-error' : '' }} ">

							{{ Form::label('email_subject', __('Email Subject'),array('class'=>'required')) }}<span class="after"></span>
							{{ Form::text('email_subject', null, array('required','id' =>"email_subject",'placeholder' => __('Enter Email Subject'),'class' => 'form-control')) }}

							@if ($errors->has('email_subject'))
							<span class="help-block">
									<strong>{{ $errors->first('email_subject') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group{{ $errors->has('email_sender_replyto') ? ' has-error' : '' }} ">

							{{ Form::label('email_sender_replyto', __('Email Sender Reply To')) }}<span class="after"></span>
							{{ Form::text('email_sender_replyto', null, array('id' =>"email_sender_replyto",'placeholder' => __('Enter Sender Reply To'),'class' => 'form-control')) }}

							@if ($errors->has('email_sender_replyto'))
							<span class="help-block">
									<strong>{{ $errors->first('email_sender_replyto') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">	
					<div class="col-md-8">
						<div class="form-group{{ $errors->has('email_message') ? ' has-error' : '' }} ">

							{{ Form::label('email_message', __('Email Message'),array('class'=>'required')) }}<span class="after"></span>
							{{ Form::textarea('email_message', null, array('required','id' =>"email_message",'placeholder' => __('Enter Email Message'),'class' => 'form-control')) }}

							@if ($errors->has('email_message'))
							<span class="help-block">
									<strong>{{ $errors->first('email_message') }}</strong>
							</span>
							@endif
						</div>
					</div>	
					<div class="col-md-4">
						<div class="form-group">
							{{ Form::label('replacement_variable', __('Replacement Variable')) }}<span class="after"></span>
							<div class="text-green" id="replacement_variable">
								<span style="user-select: all; cursor: pointer;margin-right:5px">[[event_name]]</span>
								<span style="user-select: all; cursor: pointer;margin-right:5px">[[event_website]]</span>
								<span style="user-select: all; cursor: pointer;margin-right:5px">[[event_description]]</span>
								<span style="user-select: all; cursor: pointer;margin-right:5px">[[event_org_name]]</span>
								<span style="user-select: all; cursor: pointer;margin-right:5px">[[event_org_website]]</span>
								<span style="user-select: all; cursor: pointer;margin-right:5px">[[certificate_gdrive_link]]</span>
								<span style="user-select: all; cursor: pointer;margin-right:5px">[[certificate_web_link]]</span>
							</div>
							<div class="text-green" id="replacement_text">
							</div>
						</div>
						<div class="form-group">
							{{ Form::label('map_field', __('Map Field')) }}<span class="after"></span>
							<table id="map_field" class="table table-bordered d-none">
							</table>
						</div>
					</div>		
				</div>		
				<div class="row">
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('email_sender_name') ? ' has-error' : '' }} ">

							{{ Form::label('email_sender_name', __('Email Sender Name')) }}<span class="after"></span>
							{{ Form::text('email_sender_name', null, array('id' =>"email_sender_name",'placeholder' => __('Enter Email Sender Name'),'class' => 'form-control')) }}

							@if ($errors->has('email_sender_name'))
							<span class="help-block">
									<strong>{{ $errors->first('email_sender_name') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('email_sender_email') ? ' has-error' : '' }} ">

							{{ Form::label('email_sender_email', __('Email Sender Email')) }}<span class="after"></span>
							{{ Form::text('email_sender_email', null, array('id' =>"email_sender_email",'placeholder' => __('Enter Email Sender Email'),'class' => 'form-control')) }}

							@if ($errors->has('email_sender_email'))
							<span class="help-block">
									<strong>{{ $errors->first('email_sender_email') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('email_recipient_cc') ? ' has-error' : '' }} ">

							{{ Form::label('email_recipient_cc', __('Email Recipient CC')) }}<span class="after"></span>
							{{ Form::text('email_recipient_cc', null, array('id' =>"email_recipient_cc",'placeholder' => __('Enter Email Recipient CC'),'class' => 'form-control')) }}

							@if ($errors->has('email_recipient_cc'))
							<span class="help-block">
									<strong>{{ $errors->first('email_recipient_cc') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group{{ $errors->has('email_recipient_bcc') ? ' has-error' : '' }} ">

							{{ Form::label('email_recipient_bcc', __('Email Recipient BCC')) }}<span class="after"></span>
							{{ Form::text('email_recipient_bcc', null, array('id' =>"email_recipient_bcc",'placeholder' => __('Enter Recipient BCC'),'class' => 'form-control')) }}

							@if ($errors->has('email_recipient_bcc'))
							<span class="help-block">
									<strong>{{ $errors->first('email_recipient_bcc') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<h4 class="border-bottom pt-4 mb-3 text-primary">{{__('Settings')}}</h4>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group{{ $errors->has('attach_certificate') ? ' has-error' : '' }} ">

							{{ Form::label('attach_certificate', __('Attach Certificate'),array('class'=>'required')) }}<span class="after"></span>
							{{ Form::select('attach_certificate', array('Yes'=> __('Yes'), 'No'=> __('No')),null, array('id' =>"attach_certificate", 'placeholder' => __('Please select Attach Certificate'),'class' => 'form-control')) }}

							@if ($errors->has('attach_certificate'))
							<span class="help-block">
									<strong>{{ $errors->first('attach_certificate') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-4 d-none">
						<div class="form-group{{ $errors->has('public_link') ? ' has-error' : '' }} ">

							{{ Form::label('public_link', __('Enable Public Link')) }}<span class="after"></span>
							{{ Form::select('public_link', array('Off'=> __('Off'), 'On'=> __('On')),null, array('id' =>"public_link", 'placeholder' => __('Please select public link'),'class' => 'form-control')) }}

							@if ($errors->has('public_link'))
							<span class="help-block">
									<strong>{{ $errors->first('public_link') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-4 d-none">
						<div class="form-group{{ $errors->has('scheduled_at') ? ' has-error' : '' }} ">

							{{ Form::label('scheduled_at', __('Schedule Date')) }}<span class="after"></span>
							{{ Form::text('scheduled_at', null, array('required','id' =>"scheduled_at",'placeholder' => __('Enter select scheduled date'),'class' => 'form-control')) }}

							@if ($errors->has('scheduled_at'))
							<span class="help-block">
									<strong>{{ $errors->first('scheduled_at') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<div class="box-footer">
					@if(empty($event))
						<button type="submit" class="btn btn-primary btn-dsbl">{{__('Add New Event')}}</button>
					@else
						<button type="submit" class="btn btn-primary btn-dsbl">{{__('Update Event')}}</button>
					@endif
				</div>
				<!-- /.box-body -->
			</>
			{{ Form::close() }}
		</div>
	</section>

</div>
@endsection

@push('inline-scripts')

<script type='text/javascript'>
		var selectedType = "";
    var appId = "{{ env('GOOGLE_APP_KEY') }}";
		var clientId = "{{ env('GOOGLE_CLIENT_ID') }}"
		var oauthToken="";
		
    var pickerApiLoaded = false;
	window.addEventListener('turbolinks:load', function () {

    


		$(document).ready(function(){
			$('#google_account_id').trigger('change');
			@if(!empty($event))
				var dataResponse={!! $data !!};
				processFileResponse(dataResponse);
			@endif
		});  



	
	});

	// Use the Google API Loader script to load the google.picker script.
    function loadPicker() {
      gapi.load('picker', {'callback': onPickerApiLoad});
    }
		function onPickerApiLoad() {
      pickerApiLoaded = true;
    }
// Create and render a Picker object for searching images.
    function googlePicker(type) {

    	// for validate sequnce of selection
    	if(type == "google_sheet_id"){
    		if($('#google_slide_id').val() == '' || $('#google_slide_id').val() == null){
    			alert("Please first select certificate google slide");
    			return;
    		}
    	}

    	if(type == "google_upload_folder"){
    		if($('#google_sheet_id').val() == '' || $('#google_sheet_id').val() == null){
    			alert("Please first select attendees google sheet");
    			return;
    		}
    	}

			//Set Mime Type
			mimetype = "";
			if(type == "google_slide_id"){
				mimetype = 'application/vnd.google-apps.presentation';
			}else if(type == "google_sheet_id"){
				mimetype = 'application/vnd.google-apps.spreadsheet';
			}else if(type == "google_upload_folder"){	
				mimetype = 'application/vnd.google-apps.folder';
			}
			selectedType = type;
			
      if (pickerApiLoaded && oauthToken) {		
				var view = new google.picker.DocsView()
          //.setIncludeFolders(true) 
          .setMimeTypes(mimetype)
          .setSelectFolderEnabled(true);
				
        var picker = new google.picker.PickerBuilder()
            .enableFeature(google.picker.Feature.NAV_HIDDEN)
            //.enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
            .enableFeature(google.picker.Feature.MINE_ONLY)
            .setAppId(appId)
            .setOAuthToken(oauthToken)
            .addView(view)
            .addView(new google.picker.DocsUploadView())
            .setDeveloperKey(appId)
            .setCallback(pickerCallback)
						//.setOrigin()
            .build();        
         picker.setVisible(true);
      }else{
				alert("Please select any google account before select file");
			}
    }

    // A simple callback implementation.
    function pickerCallback(data) {
      if (data.action == google.picker.Action.PICKED) { //Action.CANCEL
				jQuery("#"+selectedType).val(data.docs[0].id);
				jQuery("#"+selectedType+"_name").val(data.docs[0].name);
				jQuery("#"+selectedType+"_label").html("<b>Selected "+data.docs[0].type+": </b>"+data.docs[0].name);
				jQuery("#"+selectedType+"_label").parents(".col-md-4").find("a").addClass("btn-success");
				processfile(selectedType)
      }
    }
	function onGoogleAccountChange(){
			
				accountId = $('#google_account_id').val();
				if(accountId ==""){
					return false;
				}
				$(".loader").show();
				var url = "{{ route('adminarea.certificates.accounts.getaccesstoken') }}";
				$.ajax({
						url: url,
						type: "POST",	
						data: {"_token": "{{ csrf_token() }}",'accountId':accountId},
						dataType: "json",						
						success:function(response) {
							$(".loader").hide();	
							if(response.status=="success"){
								oauthToken = response.token;
							}else{								
								$('#google_account_id').val("");
								alert(response.message);
							}
						},
						error:function(response) {
							$(".loader").hide();
							$('#google_account_id').val("");
							alert("Unable to select this account, Please reload page and try again.");
						}
				});
			  
		}
	function processfile(type){
			if(type=="google_sheet_id"){
				accountId = $('#google_account_id').val();
				slideId = jQuery("#google_slide_id").val();
				sheetId = jQuery("#google_sheet_id").val();
				var url = "{{ route('adminarea.certificates.accounts.processfile') }}";
				$(".loader").show();
				$.ajax({
						url: url,
						type: "POST",	
						data: {"_token": "{{ csrf_token() }}",'accountId':accountId,slideId:slideId,sheetId:sheetId},
						dataType: "json",						
						success:function(response) {
							$(".loader").hide();
							if(response.status=="success"){
								processFileResponse(response);
							}
						},
						error:function(response) {
							$(".loader").hide();
							//alert("Unable to select this account, Please reload page and try again.");
						}
				});
			}
		}

		function fieldValueChange(){
		 		var email_recipient_field=[];
		 		var email_recipient_options=[];
		 		var email_recipient_optionsFound=[];
				$('.field-select > option').each(function(){
					val = $(this).val();
					if(jQuery.inArray( val, email_recipient_options )==-1){
						email_recipient_options.push(val);
					}
				});
				
		 		$('.field-select').each(function () {		      		
					var fieldName=$(this).attr('data-mapField');
					$(this).find('option').eq($(this).data('select')).attr('selected','selected')						
					var fieldValue=$(this).val();
					if(!fieldValue){
					}
					
					var obj = { "fieldName" : fieldName, "fieldValue" : fieldValue }; 
					email_recipient_field.push(obj);
					email_recipient_optionsFound.push(fieldValue);
				});
				//Push Unmapped variable
				$.each(email_recipient_options, function (i, value) {
					if(jQuery.inArray( value, email_recipient_optionsFound )==-1){
						var obj = { "fieldName" : i, "fieldValue" : value }; 
						email_recipient_field.push(obj);
					}
				});				
				var myObj = {};
				$.each(email_recipient_field, function (i, value) {
						myObj[value.fieldName] = value.fieldValue;
				});  
				$('#email_recipient_field').val(JSON.stringify(myObj));
		}


		function processFileResponse(response){
			html = "";
			mapHtml = "";
			
			mapSelect = "<select class='form-control input-sm field-select' onchange='fieldValueChange()' data-mapField data-select>";
			$( response.sheet ).each(function( ind,val ) {
				html+= '<span style="user-select: all; cursor: pointer;margin-right:5px">[['+val+']]</span>';
				mapSelect+= '<option value="'+val+'">'+val+'</option>';
			});
			mapSelect += "</select>";

			$( response.slide ).each(function( index,value ) {
				if(!jQuery.isNumeric(value)){
					mapHtml+= '<tr><td>'+value+'</td><td>'+mapSelect.replace("data-mapField","data-mapField='"+value+"'").replace("data-select","data-select='"+index+"'")+'</td></tr>';					
				}
			});
			$("#replacement_text").html(html).removeClass('d-none');
			$("#map_field").html(mapHtml).removeClass('d-none');
			fieldValueChange();
		}

</script>
<!-- The Google API Loader script. -->
<script type="text/javascript" src="https://apis.google.com/js/api.js?onload=loadPicker"></script>

@endpush


