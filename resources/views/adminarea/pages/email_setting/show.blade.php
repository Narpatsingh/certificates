@extends('layouts.app')

@section('title', __('Email Setting Deta'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ 'Email Setting Detail' }}</h3>
                    <a class="btn btn-default pull-right" href="{{ route('email_settings.index') }}">{{ 'Back' }}</a>
                </div>
                <div class="table-responsive-lg table-responsive-sm table-responsive-md table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>{{ 'Email type' }}</th>
                                <td><p>{{ $emailSetting->type }}</p></td>
                            </tr>
                            <tr>
                                <th>{{ 'Email Host' }}</th>
                                <td><p>{{ $emailSetting->host }}</p></td>
                            </tr>
                            <tr>
                                <th>{{ 'Email port' }}</th>
                                <td><p>{{ $emailSetting->port }} </p></td>
                            </tr>
                            <tr>
                                <th>{{ 'Email Encryption' }}</th>
                                <td><p>{{ $emailSetting->encryption }} </p></td>
                            </tr>
                            <tr>
                                <th>{{ 'Email Username' }}</th>
                                <td><p>{{ $emailSetting->username }} </p></td>
                            </tr>
                            <tr>
                                <th>{{ 'Email Password' }}</th>
                                <td><p>{{ $emailSetting->password }} </p></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection