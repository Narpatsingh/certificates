{{-- Master Layout --}}
@extends('cortex/foundation::adminarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ config('app.name').' » Events' }}
@endsection


{{-- Main Content --}}
@section('content')
	<div class="content-wrapper">
        <section class="content-header">
            <h1>
                <ol class="breadcrumb">
                    <li><a href="{{route('adminarea.home')}}"><i class="fa fa-dashboard"></i>Cortex</a></li>
    	            <li class="active">Events</li>
	            </ol>
            </h1>
        </section>

        {{-- Main content --}}
        <section class="content">
        	<div class="box">
	        	<div class="box-header with-border">
	        		<h3 class="box-title">Events</h3> 
	        		<a href="{{ route('adminarea.certificates.event_create') }}" class="btn btn-primary pull-right btn-sm ripple">
	        			<i class="fa fa-plus"></i> Add
					</a>
				</div>
				<div class="box-body">

					<div class='table-responsive'>
							<table class="table table-bordered" id="events-table">
									<thead>
											<tr>
													<th>{{ 'Name' }}</th>
													<th>{{ 'Website' }}</th>
													<th>{{ 'Org. Name' }}</th>
													<th>{{ 'Org. Website' }}</th>
													<th>{{ 'Added On' }}</th>
													<th>{{ 'Status' }}</th>
													<th>{{ 'Action' }}</th>
											</tr>
									</thead>
									<tbody>												
									</tbody>
							</table>
					</div>				
					
				</div>
			</div>
		</section>
	</div>
@endsection
 
@push('inline-scripts')
<script src="/js/datatables.js?id=e97e8dee65f190dac41a" defer></script>
    <script src="http://cortex.local/Editor/js/dataTables.editor.min.js"  defer></script>
    <script src="http://cortex.local/Editor/js/editor.bootstrap4.min.js" defer></script>
<script type='text/javascript'>
	
	window.addEventListener('turbolinks:load', function () {
	
	    $('#events-table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: "{{ route('adminarea.certificates.event_index') }}",
	        columns: [
	            
	            { data: 'name', name: 'name' },
	            { data: 'website', name: 'website' },
	            { data: 'org_name', name: 'org_name' },
	            { data: 'org_website', name: 'org_website' },
	            { 

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
	            	"sWidth": "20%",
		            mRender: function (data, type, row) {
		            	

		                var response =  row.status+' ';
									
						if(row.status == 'Pending'){
							response+='<a title="{{'Import Receipients'}}" class="btn btn-success btn-sm" href="{{ URL::to('/') }}/import_certificate/'+ row.id +'">Import Receipients</i></a>';
						}
						if(row.status == 'Importing Failed'){
							response+='<a title="{{'ReImport Receipients'}}" class="btn btn-success  btn-sm reimport_certificate" href="{{ URL::to('/') }}/import_certificate/'+ row.id +'">ReImport Receipients</i></a>';
						}
						if(row.status == 'Imported'){
							response+='<a title="{{'Generated Certificate'}}" class="btn btn-success  btn-sm" href="{{ URL::to('/') }}/generate_certificate/'+ row.id +'">Generate Certificates</i></a>';
						}
						if(row.status == 'Generating Failed'){
							response+='<a title="{{'ReGenerated Certificate'}}" class="btn btn-success btn-sm regenerate_certificate" href="{{ URL::to('/') }}/generate_certificate/'+ row.id +'">ReGenerate Certificates</i></a>';
						}
						if(row.status == 'Generated'){
							response+='<a title="{{'Send Certificate'}}" class="btn btn-success btn-sm" href="{{ URL::to('/') }}/send_certificate/'+ row.id +'">Send Certificates</i></a>';
						}
						if(row.status == 'Sending Failed'){
							response+='<a title="{{'ReSend Certificate'}}" class="btn btn-success btn-sm resend_certificate" href="{{ URL::to('/') }}/send_certificate/'+ row.id +'">ReSend Certificates</i></a>';
						}	
						response+='</div>';
						return response;
		            } 
	            },
	            {
		            mRender: function (data, type, row) {
		            	

		                var response =  '<div class="btn-group btn-group-sm">'+
									'<a title="{{'View Receipients & Certificates'}}" class="btn btn-default" href="{{ URL::to('/') }}/certificates/'+ row.id +'"><i class="fa fa-certificate"></i></a>'+
									'<a title="{{'View Event Information'}}" class="btn btn-default" href="{{ URL::to('/') }}/events/'+ row.id +'"><i class="fa fa-eye"></i></a>'+
									'<a title="{{'Edit Event Information'}}" class="btn btn-default" href="{{ URL::to('/') }}/events/'+ row.id +'/edit"><i class="fa fa-edit"></i></a>'+
									'</div>';
									
						return response;
		            }
		        }
	        ]
	    });

});
</script>
@endpush