@extends('layouts.app')

@section('title', __('Certificates'))

@section('content')
	<div class="row justify-content-center">
			<div class="col-md-12">
					<div class="box box-primary">
							<div class="box-header with-border">
									<h3 class="box-title">{{ $event->name }} {{ __('Certificates') }}</h3>
									<a class="btn btn-default btn-sm pull-right" href="{{ route('events.index') }}">{{__('Back')}}</a>
							</div>
						 <!-- /.box-header -->
							<div class="box-body">
									<div class='table-responsive'>
											<table class="table table-bordered" id="certificates-table">
													<thead>
															<tr>
																	<th>{{ 'Email' }}</th>
																	<th>{{ 'Payload' }}</th>
																	<th>{{ 'Added On' }}</th>
																	<th>{{ 'Action' }}</th>
															</tr>
													</thead>
											</table>
									</div>
							</div>
					</div>
			</div>
	</div>
@endsection

@push('js')
<script>
$(function() {
    $('#certificates-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('certificates.index',$eventId) }}",
        columns: [
            { data: 'email', name: 'email' },
			{ 
				"sWidth": "50%",
				mRender: function (data, type, row) {
					payload = row.payload.replace(/&quot;/g,'').replace(/\{/g,'').replace(/\}/g,'').replace(/\,/g,', ');								
					return '<span title="'+payload+'">'+payload+"</span>";
				}
						
			},
           
		  { "sWidth": "15%",
				mRender: function (data, type, row) {
					var d = new Date(row.created_at);
							dformat = [("0" + (d.getMonth() + 1)).slice(-2),
				           ("0" + d.getDate()).slice(-2),
				           d.getFullYear()].join('-')+' '+
				          [d.getHours(),
				           d.getMinutes(),
				           d.getSeconds()].join(':');				              
							return   dformat;
					}
	    },
						{
							mRender: function (data, type, row) {
								return '<div class="btn-group btn-group-sm"><a title="Regenerate" class="btn btn-default" href="{{ URL::to('/') }}/single_generate_certificate/'+ row.id +'"><i class="fa fa-refresh"></i></a><a title="Resend" class="btn btn-default"  href="{{ URL::to('/') }}/single_send_certificate/'+ row.id +'"><i class="fa fa-repeat"></i></a><a title="Download" class="btn btn-default" download  href="{{ URL::to('/') }}/event_certificates/'+ row.event_id +'/'+ row.certificate_pdf_file +'"><i class="fa fa-download"></i></a><a title="PDF Download" class="btn btn-default" target="_blank"  href="{{ URL::to('/') }}/event_certificates/'+ row.event_id +'/'+ row.certificate_pdf_file +'"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></div>'
							}
						}
        ]
    });
});
</script>
@endpush