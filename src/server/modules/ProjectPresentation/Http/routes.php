<?php

Route::group(['namespace' => 'Modules\ProjectPresentation\Http\Controllers'], function()
{
	Route::get('/', 'ProjectPresentationController@index');
});