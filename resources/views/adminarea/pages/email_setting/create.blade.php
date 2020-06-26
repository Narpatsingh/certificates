@extends('layouts.app')

@section('title', __('Email Setting'))


@section('content')
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">{{__('Email Setting')}}</h3>
		</div>
		<div class="box-body">
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#tab_smtp">{{__('SMTP')}}</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab_mailgun">{{__('Mailgun')}}</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab_ses">{{__('Amazon SES')}}</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab_postmark">{{__('Postmark')}}</a>
				</li>
			</ul>						
			<!-- Tab panes -->
			<div class="tab-content">			
				<div id="tab_smtp" class="tab-pane active">
					{{ Form::model($emailSetting,['method' => 'POST' ,'route' => ['save_settings'], 'id' => 'frm_smtp_settings' ,'class'=>'multiple-save','novalidate'=>true,'files'=>true]) }}
					{{ Form::hidden('smtp_type', 'SMTP')}}
					{{ Form::hidden('id', $id)}}
					{{ Form::hidden('send_mail', 'no')}}
					<h5 class="border-bottom pt-4 mb-3 text-primary">
						{{ Form::checkbox('is_smtp', '1',null,array('id'=>'is_smtp'))}} 
						<label for="is_smtp">{{__('SMTP Setting')}}</label>
					</h5>
					<div class="row">
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('smtp_host_address') ? ' has-error' : '' }} ">

										{{ Form::label('smtp_host_address', __('Email Host'),array('class'=>'required')) }}<span class="after"></span>
										{{ Form::text('smtp_host_address', null, array('required','id' =>"smtp_host_address",'placeholder' => __('Enter Email Host'),'class' => 'form-control')) }}

										@if ($errors->has('smtp_host_address'))
										<span class="help-block">
												<strong>{{ $errors->first('smtp_host_address') }}</strong>
										</span>
										@endif
								</div>
						</div>
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('smtp_host_port') ? ' has-error' : '' }} ">

										{{ Form::label('smtp_host_port', __('Email Port'),array('class'=>'required')) }}<span class="after"></span>
										{{ Form::text('smtp_host_port', null, array('required','id' =>"smtp_host_port",'placeholder' => __('Enter Email Port'),'class' => 'form-control')) }}

										@if ($errors->has('smtp_host_port'))
										<span class="help-block">
												<strong>{{ $errors->first('smtp_host_port') }}</strong>
										</span>
										@endif
								</div>
						</div>
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('smtp_encryption') ? ' has-error' : '' }} ">

										{{ Form::label('smtp_encryption', __('Encryption'),array('class'=>'required')) }}<span class="after"></span>
										{{ Form::select('smtp_encryption', array('ssl'=> __('SSL'),'tsl'=> __('TSL')),null, array('id' =>"smtp_encryption", 'empty' => __('Please select Encryption'),'class' => 'form-control')) }}
										@if ($errors->has('smtp_encryption'))
										<span class="help-block">
												<strong>{{ $errors->first('smtp_encryption') }}</strong>
										</span>
										@endif
								</div>
						</div>
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('smtp_server_username') ? ' has-error' : '' }} ">

										{{ Form::label('smtp_server_username', __('Email Username'),array('class'=>'required')) }}<span class="after"></span>
										{{ Form::text('smtp_server_username', null, array('required','id' =>"smtp_server_username",'placeholder' => __('Enter Email Username'),'class' => 'form-control')) }}

										@if ($errors->has('smtp_server_username'))
										<span class="help-block">
												<strong>{{ $errors->first('smtp_server_username') }}</strong>
										</span>
										@endif
								</div>
						</div>						
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('smtp_server_password') ? ' has-error' : '' }} ">

										{{ Form::label('smtp_server_password', __('Email Password'),array('class'=>'required')) }}<span class="after"></span>
										{{ Form::text('smtp_server_password', null, array('required','id' =>"smtp_server_password",'type'=>'password','placeholder' => __('Enter Email Password'),'class' => 'form-control')) }}

										@if ($errors->has('smtp_server_password'))
										<span class="help-block">
												<strong>{{ $errors->first('smtp_server_password') }}</strong>
										</span>
										@endif
								</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary btn-dsbl">{{__('Save')}}</button>
						<button type="submit" class="btn btn-primary btn-dsbl send_mail">{{__('Save & Send Test Email')}}</button>
					</div>
					{{ Form::close() }}
				</div>
				<div id="tab_mailgun" class="tab-pane">
					{{ Form::model($emailSetting,['method' => 'POST' ,'route' => ['save_settings'], 'id' => 'frm_mailgun_settings' ,'class'=>'multiple-save','novalidate'=>true,'files'=>true]) }}
					{{ Form::hidden('mailgun_type', 'MAILGUN')}}
					{{ Form::hidden('id', $id)}}
					{{ Form::hidden('send_mail', 'no')}}
					<h5 class="border-bottom pt-4 mb-3 text-primary">
						{{ Form::checkbox('is_mailgun', '1',null,array('id'=>'is_mailgun'))}} 
						<label for="is_mailgun">{{__('Mailgun Setting')}}</label>
					</h5>
					<div class="row">
						{{ Form::open(['method' => 'POST' ,'route' => ['save_settings'], 'id' => 'frm_email_settings' ,'class'=>'multiple-save','novalidate'=>true,'files'=>true]) }}
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('mailgun_domain') ? ' has-error' : '' }} ">

										{{ Form::label('mailgun_domain', __('Email Domain'),array('class'=>'required')) }}<span class="after"></span>
																	@if(empty($emailSetting))
												{{ Form::text('mailgun_domain', null, array('required','id' =>"mailgun_domain",'placeholder' => __('Enter Domain'),'class' => 'form-control')) }}
																	@else
																			{{ Form::text('mailgun_domain', $emailSetting->host, array('required','id' =>"mailgun_domain",'placeholder' => __('Enter Domain'),'class' => 'form-control')) }}
																	@endif
										@if ($errors->has('mailgun_domain'))
										<span class="help-block">
												<strong>{{ 'Please Enter Email domain.' }}</strong>
										</span>
										@endif
								</div>
						</div>
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('mailgun_secret') ? ' has-error' : '' }} ">

										{{ Form::label('mailgun_secret', __('Email Secret Key'),array('class'=>'required')) }}<span class="after"></span>
																	@if(empty($emailSetting))
												{{ Form::text('mailgun_secret', null, array('required','id' =>"mailgun_secret",'placeholder' => __('Enter Email Secret Key'),'class' => 'form-control')) }}
																	@else
																			{{ Form::text('mailgun_secret', $emailSetting->password, array('required','id' =>"mailgun_secret",'placeholder' => __('Enter Email Secret Key'),'class' => 'form-control')) }}   
																	@endif
										@if ($errors->has('mailgun_secret'))
										<span class="help-block">
												<strong>{{ 'Please Enter Email Secret Key.' }}</strong>
										</span>
										@endif
								</div>
						</div>
						<div class="col-md-4">
								<div class="form-group{{ $errors->has('mailgun_endpoint') ? ' has-error' : '' }} ">

										{{ Form::label('mailgun_endpoint', __('Email Endpoint'),array('class'=>'required')) }}<span class="after"></span>
																	@if(empty($emailSetting))
												{{ Form::text('mailgun_endpoint', null, array('required','id' =>"mailgun_endpoint",'placeholder' => __('Enter Endpoint'),'class' => 'form-control')) }}
																	@else
																			{{ Form::text('mailgun_endpoint', $emailSetting->host, array('required','id' =>"mailgun_endpoint",'placeholder' => __('Enter Endpoint'),'class' => 'form-control')) }}
																	@endif
										@if ($errors->has('mailgun_endpoint'))
										<span class="help-block">
												<strong>{{ 'Please Enter Email Endpoint.' }}</strong>
										</span>
										@endif
								</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary btn-dsbl">{{__('Save')}}</button>
						<button type="submit" class="btn btn-primary btn-dsbl send_mail">{{__('Save & Send Test Email')}}</button>
					</div>
					{{ Form::close() }}
				</div>
				<div id="tab_ses" class="tab-pane">
				{{ Form::model($emailSetting,['method' => 'POST' ,'route' => ['save_settings'], 'id' => 'frm_ses_settings' ,'class'=>'multiple-save','novalidate'=>true,'files'=>true]) }}
				{{ Form::hidden('ses_type', 'SES')}}
				{{ Form::hidden('id', $id)}}
				{{ Form::hidden('send_mail', 'no')}}
				<h5 class="border-bottom pt-4 mb-3 text-primary">
					{{ Form::checkbox('is_ses', '1',null,array('id'=>'is_ses'))}} 
					<label for="is_ses">{{__('Amazon SES Setting')}}</label>
				</h5>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group{{ $errors->has('ses_key') ? ' has-error' : '' }} ">
							{{ Form::label('ses_key', __('SES Key'),array('class'=>'required')) }}<span class="after"></span>
												@if(empty($emailSetting))
									{{ Form::text('ses_key', null, array('required','id' =>"ses_key",'placeholder' => __('Enter SES key'),'class' => 'form-control')) }}
												@else
														{{ Form::text('ses_key', $emailSetting->username, array('required','id' =>"ses_key",'placeholder' => __('Enter SES key'),'class' => 'form-control')) }}
												@endif
							@if ($errors->has('ses_key'))
							<span class="help-block">
									<strong>{{ 'Please Enter SES Key.' }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group{{ $errors->has('ses_secret') ? ' has-error' : '' }} ">

							{{ Form::label('ses_secret', __('SES Secret Key'),array('class'=>'required')) }}<span class="after"></span>
												@if(empty($emailSetting))
									{{ Form::text('ses_secret', null, array('required','id' =>"ses_secret",'placeholder' => __('Enter SES secret key'),'class' => 'form-control')) }}
												@else
														{{ Form::text('ses_secret', $emailSetting->password, array('required','id' =>"ses_secret",'placeholder' => __('Enter SES secret key'),'class' => 'form-control')) }}
												@endif
							@if ($errors->has('ses_secret'))
							<span class="help-block">
									<strong>{{ 'Please Enter SES Secret Key.' }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group{{ $errors->has('ses_region') ? ' has-error' : '' }} ">

							{{ Form::label('ses_region', __('Region'),array('class'=>'required')) }}<span class="after"></span>
												@if(empty($emailSetting))
									{{ Form::text('ses_region', null, array('required','id' =>"ses_region",'placeholder' => __('Enter Region'),'class' => 'form-control')) }}
												@else
														{{ Form::text('ses_region', $emailSetting->host, array('required','id' =>"ses_region",'placeholder' => __('Enter Region'),'class' => 'form-control')) }}
												@endif
							@if ($errors->has('ses_region'))
							<span class="help-block">
									<strong>{{ 'Please Enter Region.' }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary btn-dsbl">{{__('Save')}}</button>
					<button type="submit" class="btn btn-primary btn-dsbl send_mail">{{__('Save & Send Test Email')}}</button>
				</div>
				{{ Form::close() }}
				</div>
				<div id="tab_postmark" class="tab-pane">
					{{ Form::model($emailSetting,['method' => 'POST' ,'route' => ['save_settings'], 'id' => 'frm_postmark_settings' ,'class'=>'multiple-save','novalidate'=>true,'files'=>true]) }}
					{{ Form::hidden('postmark_type', 'POSTMARK')}}
					{{ Form::hidden('id', $id)}}
					{{ Form::hidden('send_mail', 'no')}}
					<h5 class="border-bottom pt-4 mb-3 text-primary">
						{{ Form::checkbox('is_postmark', '1',null,array('id'=>'is_postmark'))}} 
						<label for="is_postmark">{{__('Postmark Setting')}}</label>
					</h5>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group{{ $errors->has('postmark_token') ? ' has-error' : '' }} ">

									{{ Form::label('postmark_token', __('Token'),array('class'=>'required')) }}<span class="after"></span>
															@if(empty($emailSetting))
											{{ Form::text('postmark_token', null, array('required','id' =>"postmark_token",'placeholder' => __('Enter Token'),'class' => 'form-control')) }}
															@else
																	{{ Form::text('postmark_token', $emailSetting->password, array('required','id' =>"postmark_token",'placeholder' => __('Enter Token'),'class' => 'form-control')) }}
															@endif
									@if ($errors->has('postmark_token'))
									<span class="help-block">
											<strong>{{ 'Please Enter Token.' }}</strong>
									</span>
									@endif
							</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary btn-dsbl">{{__('Save')}}</button>
						<button type="submit" class="btn btn-primary btn-dsbl send_mail">{{__('Save & Send Test Email')}}</button>
					</div>
					{{ Form::close() }}
				</div>
			</div>
			<!-- /.box-body -->
		</div>
	</div>
@endsection

@push('js')

@endpush