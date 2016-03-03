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
		Route::post('/preferences', 'SeedBankController@postPreferences');
		Route::post('/add-pictures', 'SeedBankController@postAddPicture');
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
		Route::get('/seedm/{id}', function ($id) {
			$user = \Auth::user();
			$seed = \Caravel\Seed::findOrFail($id);
			$update_seed = ($seed->user_id == $user->id);
			if (($seed->public) || ($update_seed))
			{
				$seed->variety;
				$seed->species;
				$seed->family;
				$seed->cookings();
				$seed->medicines();
				if ($seed->pictures->count()){
				  $picture = $seed->pictures->first();
				} else {
				  $picture = false;
				}
				//dd($seed);

				return view('seedbank::seed_modal')
				  ->with('seed', $seed)
				  ->with('picture', $picture)
				  ->with('update_seed', $update_seed);
      /*->with('usermessages', $userMessages)
      ->with('unreadmessages', $unreadmessages)
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
	  ->with('active', ['home' => true]);*/
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
		Route::get('/seeds', function () {
			$user = \Auth::user();
			return $user->seeds()->paginate(5);
		});
		/*Route::post('/add-pictures', function (Request $request) {
			// TODO: Limit number of picture by seed?
			$user = \Auth::user();
			if ($request->has('seed_id')){
				$seed = \Caravel\Seed::findOrFail($request->input('seed_id'));
			} else {
				$seed = false;
			}
			if ($request->hasFile('pictures')) {
				$picture = $request->file('pictures')[0];
				$status = save_image($picture);
				if (isset($status['error'])) {
					return [ 'files' => [ ['error' => $status['error']]]];
				} else {
					if ($seed) {
						if (!$seed->user_id == $user->id){
					      return [ 'files' => [ ['error' => 'File is owned by other user']]];
						} else {
							$seed->pictures()->save($status['picture']);
						}
					}
					return [ 'files' => [ ['md5sum' => $status['picture']->md5sum, 
						                   'id' => $status['picture']->id,
										   'url' => $status['picture']->url,
										   'deleteUrl' => '/seedbank/pictures/delete/' . $status['picture']->id,
									       'deleteType' => 'GET'  ]
									   ]];
				}
			}
			return [ 'files' => [['error' => 'No files sent']]];
		});*/
		Route::get('/pictures/delete/{id}', function ($id) {
			$user = \Auth::user();
			$picture = \Caravel\Picture::findOrFail($id);
			if ($picture->seed){
				if (!$picture->seed->user_id == $user->id) { return [ "files" => [[ $picture->md5sum => false]] ]; }
			}
			$deleted = \File::delete($picture->path);
			// TODO: Might not be able to delete for some reason (permissions?)
			$picture->delete();
			return [ "files" => [[ $picture->md5sum => true]] ];
		});
		Route::get('/pictures/{md5sum}', function ($md5sum) {
			// TODO: Pass this work to nginx?
			$user = \Auth::user();
			$picture = \Caravel\Picture::where('md5sum', $md5sum)->firstOrFail();
            $file = \File::get($picture->path);
			$response = \Response::make($file,200);
			$response->header('Content-Type', 'image/jpg');
			return $response;
		});
		Route::get('/exchange/{action}/{id}', function ($action, $id) {
		  $user = \Auth::user();
		  if (in_array($action, ['accept', 'reject', 'complete'])) {
		     if (!method_exists($user,$action.'Transaction')){
		        return 'false';
			 }		
			 $output = call_user_func_array(array($user, $action.'Transaction'),array($id));
			 return "ok";
		  }
		  //return true || error;
          return (null);
		});
	});
});
