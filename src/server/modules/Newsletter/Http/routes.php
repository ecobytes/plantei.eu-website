<?php

Route::group(['prefix' => 'newsletter', 'namespace' => 'Modules\Newsletter\Http\Controllers'], function()
{
	//Route::get('/', 'NewsletterController@index');
	Route::get('/subscribed', 'NewsletterController@subscribed');
	Route::post('/', 'NewsletterController@store');
	Route::get('confirm/{key}', 'NewsletterController@confirm');
});