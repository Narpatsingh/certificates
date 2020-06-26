@extends('layouts.app')

@section('title', __('Email Setting'))

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">

                    <h3 class="box-title">{{ __('Email Setting') }}</h3>

                    <a href="{{ url(env("APP_ROOT").'/email_settings/create') }}" class="btn btn-primary pull-right btn-sm ripple">
                       <i class="fa fa-plus"></i> {{ 'Add' }}
                    </a>
                </div>
               <!-- /.box-header -->

                <div class="box-body">
                    <div class='table-responsive'>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ 'Email Type' }}</th>
                                    <th>{{ 'Email Host' }}</th>
                                    <th>{{ 'Email Port' }}</th>
                                    <th>{{ 'Email Encryption' }}</th>
                                    <th>{{ 'Added On' }}</th>
                                    <th>{{ 'Action' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($emailSettings))
                                @foreach ($emailSettings as $emailSetting)
                                    <tr>
                                        <td>{{ $emailSetting->type }}</td>
                                        <td>{{ $emailSetting->host }}</td>
                                        <td>{{ $emailSetting->port }}</td>
                                        <td>{{ $emailSetting->encryption }}</td>
                                        <td>{{ $emailSetting->created_at }}</td>
																				<td class="text-center">
																					<div class="btn-group btn-group-sm">
                                            <a title="{{'Send test email'}}" class="btn btn-info btn-sm" href="{{ route('send_testing_mail',$emailSetting->id)}}"><i class="fa fa-envelope"></i></a>
                                            <a title="{{'Edit'}}" class="btn btn-info btn-sm" href="{{ route('email_settings.edit',$emailSetting->id)}}"><i class="fa fa-edit"></i></a>
                                            <a title="{{'View'}}" class="btn btn-primary btn-sm" href="{{ route('email_settings.show',$emailSetting->id) }}"><i class="fa fa-eye"></i></a>

                                            {{ Form::open(array('route' => array('email_settings.destroy', $emailSetting->id, 'method' =>'DELETE'))) }}
                                            {{ method_field('DELETE') }}
																							{{ Form::button('<i class="fa fa-remove"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-sm','style'=>'border-radius: 0 3px 3px 0px;','onclick' => 'return confirm("Are you sure you want to delete emailSetting?")'] )  }}
                                            {{ Form::close() }}
																					</div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-danger">
                                        {{ 'No email settings found.' }}
                                    </td>
                                </tr>
                            @endif

                            </tbody>
                        </table>
                    </div>
                    <div class='paginate paginate-right'>
                        {{ $emailSettings -> links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection