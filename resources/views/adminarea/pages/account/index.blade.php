{{-- Master Layout --}}
@extends('cortex/foundation::adminarea.layouts.default')

{{-- Page Title --}}

@section('title')
    {{ config('app.name').' » Accounts' }}
@endsection


{{-- Main Content --}}
@section('content')
<style type="text/css">

.p-2 {
    padding: 0.5rem !important;
}
.card-body {
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1.25rem;
}

.card-img, .card-img-top {
    border-top-left-radius: calc(0.25rem - 1px);
    border-top-right-radius: calc(0.25rem - 1px);
}
.card-img, .card-img-top, .card-img-bottom {
    flex-shrink: 0;
    width: 100%;
}

@media (min-width: 576px){
	.card-columns .card {
	    display: inline-block;
	    width: 100%;
	}
}
.card-columns .card {
    margin-bottom: 0.75rem;
}
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.25rem;
}

.border-top {
    border-top: 1px solid #dee2e6 !important;
}

</style>
<div class="content-wrapper">
        <section class="content-header">
            <h1>
                <ol class="breadcrumb">
                    <li><a href="{{route('adminarea.home')}}"><i class="fa fa-dashboard"></i>Cortex</a></li>
    	            <li class="active">Accounts</li>
	            </ol>
            </h1>
        </section>

        {{-- Main content --}}
        <section class="content">
        	<div class="box">
	        	<div class="box-header with-border">
	        		<h3 class="box-title">Google Accounts</h3> 
	        		<a href="{{ route('adminarea.certificates.newaccount') }}" class="btn btn-primary pull-right btn-sm ripple">
	        			<i class="fa fa-plus"></i> Add new Google Account
					</a>
				</div>
				<div class="box-body">
					<div class="row card-columns">	
						@if(count($accounts))
								@foreach ($accounts as $account)
									<div class="col-md-2 col-sm-6">
										<div class="card">
											<img class="card-img-top" src="{{ $account->avatar }}" alt="{{ $account->name }}">
											<div class="card-body p-2 ">
												<h5 class="card-title">{{ $account->name }}</h5>
												<p class="card-text">{{ $account->created_at}}</p>
											</div>
											<div class="p-2 border-top">
												{{ Form::open(array('route' => array('adminarea.certificates.accounts.destroy', $account->id, 'method' =>'DELETE'))) }}
												{{ method_field('DELETE') }}
												{{ Form::button('<i class="fa fa-remove"></i> Delete Account', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm btn-block','onclick' => 'return confirm("Are you sure you want to delete google account?")'] )  }}
												{{ Form::close() }}
												<div class="pt-2"></div>
												<a href="{{ route('adminarea.certificates.accounts.getfilefolder',$account->id) }}" class="btn btn-success btn-sm btn-block">Get File & Folder</a>
											</div>
										</div>
									</div>
								@endforeach
							@else
									<div class="col-md-12">
										<div class="alert alert-warning" role="alert">
											<h4 class="alert-heading">Hurray!</h4>
											<p>Please link at least one Google Drive account.<br> <a href="{{ route('adminarea.certificates.newaccount') }}">Click Here</a> to link Google Drive account.</p>
										</div>
									</div>
							@endif										
					</div>
					<div class='pagination justify-content-end'>
					{{ $accounts -> links() }}
					</div>
				</div>
			</div>
		</section>
</div>

@endsection