<?php

Route::group(['prefix' => 'seedbank', 'namespace' => 'Modules\SeedBank\Http\Controllers'], function()
{
	Route::get('/', 'SeedBankController@index');
	Route::get('/register/{id?}', 'SeedBankController@getRegister');
	Route::post('/register/{id?}', 'SeedBankController@postRegister');
	Route::get('/search', 'SeedBankController@getSearch');
	Route::post('/search', 'SeedBankController@postSearch');
	Route::post('/autocomplete', 'SeedBankController@postAutocomplete');
	Route::get('/preferences', 'SeedBankController@getPreferences');
});
