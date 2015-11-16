<?php

Route::group(['prefix' => 'seedbank', 'namespace' => 'Modules\SeedBank\Http\Controllers'], function()
{
	Route::group(['middleware' => 'auth'], function(){
		Route::get('/', 'SeedBankController@index');
		Route::get('/myseeds', 'SeedBankController@mySeeds');
		Route::get('/register/{id?}', 'SeedBankController@getRegister');
		Route::post('/register/{id?}', 'SeedBankController@postRegister');
		Route::get('/search', 'SeedBankController@getSearch');
		Route::post('/search', 'SeedBankController@postSearch');
		Route::post('/autocomplete', 'SeedBankController@postAutocomplete');
		Route::get('/preferences', 'SeedBankController@getPreferences');
		//Route::post('/seed/{id}', 'SeedBankController@postSeed');
		Route::get('/seed/{id}', function ($id) {
			$seed = \Caravel\Seed::findOrFail($id);
			foreach(['variety', 'family', 'species'] as $field){
				$field_a = (array)DB::table($field)->select('name')->find($seed[$field . '_id']);
				$seed[$field] = $field_a['name'];
			};
			return $seed;
		});
	});
});
