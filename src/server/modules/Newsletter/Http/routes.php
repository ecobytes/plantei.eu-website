<?php

Route::group(['prefix' => 'newsletter', 'namespace' => 'Modules\Newsletter\Http\Controllers'], function()
{
	//Route::get('/', 'NewsletterController@index');
	Route::group(['middleware' => 'csrf'], function(){
		Route::post('/', 'NewsletterController@store');
	});
	Route::get('/subscribed', 'NewsletterController@subscribed');
	Route::get('confirm/{key}', 'NewsletterController@confirm');
});