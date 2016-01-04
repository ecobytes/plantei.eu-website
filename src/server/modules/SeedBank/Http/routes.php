<?php

use Illuminate\Http\Request;

function save_image($uploadedimage) {
  $file_md5 = md5_file($uploadedimage);
  $file_name = $file_md5;
  $picture = \Caravel\Picture::where('md5sum', $file_md5)->first();
  if (!$picture){
    $picture_path = '/tmp/PITCTURES/PATH';
    $uploadedimage->move($picture_path, $file_name);
    $converted_image = new Imagick($picture_path . '/' . $file_md5);
    $converted_image->setImageFormat('jpg');
    $converted_image->scaleimage(800, 800, true);
    if (filesize($picture_path . '/' . $file_name) > 200000) {
      $converted_image->setOption('jpeg:extent', '100kb');
    }
    $status = $converted_image->writeimage();
    if ($status) {
	  $picture = \Caravel\Picture::create([
	    'path' => $picture_path . '/' . $file_name,
	    'url' => '/seedbank/pictures/' . $file_md5,
	    'md5sum' => $file_md5
	  ]);
	} else {
	  return [ "error" => "File not saved"];
    }
  }
  return [ "picture" => $picture];
};

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
		Route::get('/seeds', function () {
			$user = \Auth::user();
			return $user->seeds()->paginate(5);
		});
		Route::post('/add-pictures', function (Request $request) {
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
		});
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
	});
});
