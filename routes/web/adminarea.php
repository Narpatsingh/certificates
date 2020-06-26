<?php

declare(strict_types=1);

Route::domain(domain())->group(function () {
    Route::name('adminarea.')
        ->middleware(['web', 'nohttpcache'])
        ->namespace('Sushil\Certificate\Http\Controllers\Adminarea')
        ->prefix(config('cortex.foundation.route.locale_prefix') ? '{locale}/'.config('cortex.foundation.route.prefix.adminarea') : config('cortex.foundation.route.prefix.adminarea'))->group(function () {

            

        // Commands Routes
        Route::name('certificates.')->prefix('certificates')->group(function () {
            Route::get('events')->name('event.index')->uses('EventsController@index');
            Route::get('/create')->name('event.create')->uses('EventsController@create');
            
            

            Route::Post('events')->name('events.store')->uses('EventsController@store');
            Route::Post('events/{event}')->name('events.update')->uses('EventsController@update');
            Route::delete('events/{event}')->name('events.destroy')->uses('EventsController@destroy');
            Route::get('events/{event}/edit')->name('events.edit')->uses('EventsController@edit');

            Route::get('accounts')->uses('AccountsController@index')->name('accounts.index');
            Route::post('accounts/destroy')->uses('AccountsController@destroy')->name('accounts.destroy');
            Route::post('accounts/getaccesstoken')->uses('AccountsController@getaccesstoken')->name('accounts.getaccesstoken');
            Route::post('accounts/processfile/','AccountsController@processfile')->name('accounts.processfile');
            Route::get('accounts/getfilefolder/{id}','AccountsController@getfilefolder')->name('accounts.getfilefolder');
            Route::get('newaccount', 'AccountsController@newaccount')->name('newaccount');
        });


    });
});
