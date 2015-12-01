<?php

Route::group(['prefix' => 'seedbank', 'namespace' => 'Modules\SeedBank\Http\Controllers'], function()
{
	Route::group(['middleware' => 'auth'], function(){
		Route::get('/', 'SeedBankController@index');
		Route::get('/myseeds', 'SeedBankController@mySeeds');
		Route::get('/messages', 'SeedBankController@getMessages');
		Route::get('/register/{id?}', 'SeedBankController@getRegister');
		Route::post('/register/{id?}', 'SeedBankController@postRegister');
		Route::get('/search', 'SeedBankController@getSearch');
		Route::post('/search', 'SeedBankController@postSearch');
		Route::post('/autocomplete', 'SeedBankController@postAutocomplete');
		Route::get('/preferences', 'SeedBankController@getPreferences');
		//Route::post('/seed/{id}', 'SeedBankController@postSeed');
		Route::get('/seed/{id}', function ($id) {
			$user = \Auth::user();
			$seed = \Caravel\Seed::findOrFail($id);
			if (($seed->public) && ($seed->user_id == $user->id))
			{
				$seed->variety;
				$seed->species;
				$seed->family;
				$seed->cookings();
				$seed->medicines();

				return $seed;
			}
			$seedsbank_entry = \Caravel\SeedsBank::where('seed_id',$id)
				->where('public', true)
				->firstOrFail();
			foreach(['variety', 'family', 'species'] as $field){
				$field_a = (array)DB::table($field)->select('name')->find($seed[$field . '_id']);
				$seed[$field] = $field_a['name'];
			};
			$seed['description'] = $seedsbank_entry->description;
			/* In case multiple descriptions exist in multiple SeedsBanks
				foreach(\Caravel\SeedsBank::where('seed_id', $seed->id)->get() as $seedsbank){
					$seed['description'] = $seed['description'] . $seedsbank->description . "\n";
				}
			 */
			return $seed;
		});
		Route::get('/message/get/{id}', function ($id) {
			//$message = \Caravel\Message::findOrFail($id);
			$user = \Auth::user();
			//$message = \Caravel\Message::findOrFail($id);
                       $message = $user->messageById($id);
			if (!$message->user_id == $user->id)
			{
				$message->pivot->read = true;
				$message->pivot->save();
			}
			return $message;
		});
		Route::post('/message/reply', 'SeedBankController@postMessageReply');
		Route::post('/message/send', 'SeedBankController@postMessageSend');
	});
});
