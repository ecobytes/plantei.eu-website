<?php

use Illuminate\Http\Request;

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
			if (($seed->public) && ($seed->user_id != $user->id))
			{
				$seed->variety;
				$seed->species;
				$seed->family;
				$seed->cookings();
				$seed->medicines();

				return $seed;
			}
			return [];
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
		Route::get('/exchanges', 'SeedBankController@getExchanges');
		Route::get('/user_seeds/{id}', function ($id) {
			$user = \Auth::user();
			$seed_owner = \Caravel\User::findOrFail($id);
		    $seeds = $seed_owner->seeds()
				->where('public', true)->where('available', true)
				->leftJoin('seeds_exchanges', function($join) use ($user)
				{
					$join->on('seeds.id', '=', 'seed_id')
						->where('asked_by', '=', $user->id)
						->where('completed', '<', 2)
						->where('accepted', '<>', 1);
				})
				->select('seeds_exchanges.*', 'seeds.id as id', 'seeds.common_name')->get();
			return [
				"seeds" => $seeds,
				"user" => $seed_owner
			];

		});
		Route::post('/startexchange', function (Request $request) {
			$user = \Auth::user();
			$seed_ids = $request->input('seed_ids');
			$owner_id = $request->input('user_id');

			if ((! $seed_ids) || (! $owner_id)) { return []; }
				$data = [
					'asked_to' => $owner_id, 
					'seed_ids' => $seed_ids
				];
			return $user->startTransaction($data)
				->join('seeds', 'seed_id', '=', 'seeds.id')
				->select('seeds_exchanges.*', 'seeds.common_name')->get();
		});
	});
});
