<?php namespace Modules\Seedbank\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Http\Request;
use Gate;


use Validator;
use GeoIp2\Database\Reader;


class SeedBankController extends Controller {
    public static function save_image($uploadedimage) {
      $file_md5 = md5_file($uploadedimage);
      $file_name = $file_md5;
      $picture = \Caravel\Picture::where('md5sum', $file_md5)->first();
      if (!$picture){
        $picture_path = '/tmp/PITCTURES/PATH';
        $uploadedimage->move($picture_path, $file_name);
        $converted_image = new \Imagick($picture_path . '/' . $file_md5);
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
    }


	public function prefValidator(array $data)
	{
      $user = \Auth::user();
        //dd($data);
		if (!$data['email']){unset($data['email']);};
        $rules = [
			'lon' => 'required_with:lat|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/|between:-180,180',
			'lat' => 'required_with:lon|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/|between:-180,180',
			'place_name' => 'max:255|required_with:lon,lat',
          ];
      if (! $user->name == $data['name']){
        $rules['name'] = 'required|max:255|unique:users';
      }
      if (! $user->email == $data['email']){
        $rules['email'] = 'sometimes|required|email|max:255|unique:users';
      }
      if ($data['password']){
        $rules['password'] = 'required|confirmed|min:6';
      }
	  return Validator::make($data, $rules);
	}
  
  public function index()
  {
    $user = \Auth::user();

    $seeds = \Caravel\Seed::join('users', 'users.id', '=', 'user_id' )->where('public', true)
      ->orWhere('user_id', $user->id)->orderBy('seeds.updated_at', 'desc')
      ->select('seeds.id', 'seeds.latin_name', 'seeds.common_name',
        'users.name', 'users.email', 'user_id')
      ->get()
      ->chunk(20)[0]
      ->toArray();

    $userMessages = $user->messages()->get()->sortByDesc('created_at')->chunk(4)[0]->toArray();
    $unreadmessages = 0;
    foreach($userMessages as &$m) {
      $t = array();
      if ($m['pivot']['read']){
        $t[1] = true;
        $m['pivot']['read'] = $t;
      } else {
        $unreadmessages++;
      }
    }
    return view('seedbank::home')
      ->with('seeds', $seeds)
      ->with('usermessages', $userMessages)
      ->with('unreadmessages', $unreadmessages)
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['home' => true]);
  }

  public function mySeeds()
  {
    // View for my seeds
    $user = \Auth::user();
    //dd($user->id);
    //$user = \Caravel\User::where('id', 1)->first();
    //$seeds = $user->seeds->toArray();
    $seeds = $user->seeds()->orderBy('updated_at', 'desc')->paginate(5)->setPath('/seedbank/seeds');
    $t = $seeds;
    /* $t = [];
    foreach($seeds as $seed){
      $tseed = $seed->toArray();
      foreach(['origin', 'polinization', 'direct', 'public', 'available'] as $key){
        if(isset($tseed[$key])){
          $tseed[$key] = [$tseed[$key] => true];
        }
      }
      $t[] = $tseed;
    }*/
    //dd($t);
    $transactions = $user->transactionsPending();
    //dd($transactions);
    if ($transactions['asked_by']){
      foreach($transactions['asked_by'] as &$tr){
        $ta = [];
        //pending
        if ($tr['accepted'] == 0){$ta[2]=true;}
        //canceled
        elseif ($tr['accepted'] == 1){$ta[0]=true;}
        //accepted
        elseif ($tr['accepted'] == 2){$ta[1]=true;} 
        $tr['accepted']=$ta;;
      }
    }
    if ($transactions['asked_to']){
      foreach($transactions['asked_to'] as &$tr){
        $ta = [];
        //pending
        if ($tr['accepted'] == 0){$ta[2]=true;}
        //canceled
        elseif ($tr['accepted'] == 1){$ta[0]=true;}
        //accepted
        elseif ($tr['accepted'] == 2){$ta[1]=true;} 
        $tr['accepted'] = $ta;
      }
    }
    //dd($transactions);
    return view('seedbank::myseeds')
      ->with('seeds', $t)
      ->with('transactionsBy', $transactions['asked_by']) 
      ->with('transactionsTo', $transactions['asked_to']) 
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['myseeds' => true]);
  }

  public function getMessages()
  {
    $user = \Auth::user();

    $userMessages = $user->lastMessages(10)->toArray();
      //->get()->sortByDesc('created_at')->chunk(4)[0]->toArray();
      //dd($userMessages);
    $unreadmessages = 0;
    foreach($userMessages as &$m) {
      if (($m['sender_id'] != $user->id) && ($m['read'])){
        $unreadmessages++;
        $m['enabled'] = true;
      }
      if ($m['sender_id'] == $user->id){
        $m['sent'] = true;
      }
    }
    return view('seedbank::messages')
      ->with('usermessages', $userMessages)
      ->with('unreadmessages', $unreadmessages)
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['messages' => true]);
  }

  public function getExchanges()
  {
    $user = \Auth::user();

    $transactions = $user->transactionsPending();
    if ($transactions['asked_by']){
      foreach($transactions['asked_by'] as &$tr){
        $ta = [];
        //pending
        if ($tr['accepted'] == 0){$ta[0]=true;}
        //canceled
        elseif ($tr['accepted'] == 1){$ta[1]=true;}
        //accepted
        elseif ($tr['accepted'] == 2){$ta[2]=true;} 
        $tr['accepted']=$ta;;
        $tc = [];
        //pending
        if ($tr['completed'] == 0){$tc[0]=true;}
        //canceled
        elseif ($tr['completed'] == 1){$tc[1]=true;}
        //confirmed
        elseif ($tr['completed'] == 2){$tc[2]=true;} 
        $tr['completed']=$tc;;
      }
    }
    if ($transactions['asked_to']){
      foreach($transactions['asked_to'] as &$tr){
        $ta = [];
        //pending
        if ($tr['accepted'] == 0){$ta[0]=true;}
        //canceled
        elseif ($tr['accepted'] == 1){$ta[1]=true;}
        //accepted
        elseif ($tr['accepted'] == 2){$ta[2]=true;} 
        $tr['accepted'] = $ta;
        $tc = [];
        //pending
        if ($tr['completed'] == 0){$tc[0]=true;}
        //canceled
        elseif ($tr['completed'] == 1){$tc[1]=true;}
        //confirmed
        elseif ($tr['completed'] == 2){$tc[2]=true;} 
        $tr['completed']=$tc;;
      }
    }
    //dd($transactions);
    return view('seedbank::exchanges')
      ->with('transactionsBy', $transactions['asked_by']) 
      ->with('transactionsTo', $transactions['asked_to']) 
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['exchanges' => true]);
  }


  public function getRegister($id = null)
  {
    $user = \Auth::user();
    $update = false;
    // Authorization
    if($id){
      $seed = \Caravel\Seed::findOrFail($id);
      if (Gate::denies('update-seed', $seed)){
        abort(403);
      }
    }
    //$errors = \Session::get('errors');
    if(\Session::hasOldInput()){
      $oldInput =  \Session::getOldInput();
      if(!empty($errors)){
        \View::share('errors', $errors->default->toArray());
      }
      foreach(['variety', 'family', 'species'] as $field)
      {
        if (isset($oldInput[$field]))
        {
          $field_a = (array)\DB::table($field)
            ->select('name', 'id')
            ->where('name', $oldInput[$field])
            ->first();
          if ($field_a) {
            $oldInput[$field] = $field_a;
          } else {
            $oldInput[$field] = ['id'=>'', 'name'=>$oldInput[$field] ];
          }
        }
      };
    } 
    if ($id){
      if (! isset($oldInput)) {
       $seed->variety;
       $seed->family;
       $seed->species;
       $seed->pictures;
       $oldInput = $seed->toArray();
       $oldInput['months'] = $seed->months()->lists('month')->toArray();
      } else {

      }
      $oldInput['id'] = $id;
      $update = true;
    }
    //$t = [];
    foreach(['origin', 'polinization', 'direct'] as $key){
      if(isset($oldInput[$key])){
        $oldInput[$key] = [$oldInput[$key] => true];
      }
    }

    if(isset($oldInput['months'])){
      $o = array();
      foreach($oldInput['months'] as $i){
        $o[$i] = true;
      }
      $oldInput['months'] = $o;
    }
    if (! isset($oldInput)){
      $oldInput = [];
    }
    return view('seedbank::registerseed', ['update' => $update])
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['myseeds' => true])
      ->with('oldInput', $oldInput); 
  }

  public function postRegister(Request $request)
  {
    // if error with form
    //dd($request->input());
    $this->validate($request, [
      'common_name' => 'required',
      //'origin' => 'required',
    ]);

    if (!($request->input('confirmed') == "1")){
	  $farming = false;
	  $pictures = true;
	  $taxonomy = false;
      $formInput = $request->input();
      if (isset($formInput['months'])) {
          $farming = true;
	      $months = array();
	      foreach (range(1, 12) as $i) {
	          if (in_array($i, $formInput['months'])) { 
	        	  $months[$i] = ['month' => true];
	          } else { 
	        	  $months[$i] = ['month' => false];
	          }
          }
      } else { $months = false; }



	  if (($formInput['variety']) || ($formInput['species']) || ($formInput['family']) || ($formInput['latin_name'])){
	    $taxonomy = true;
	  }

      return view('testmodule::snippet')
        ->with('title', 'some nice title')
        ->with('months', $months)
		->with('seed', $formInput)
		->with('pictures', $pictures)
		->with('farming', $farming)
        ->with('taxonomy', $taxonomy)
        ->with('deletebutton', \Lang::get('seedbank::messages')['delete']);

    }
    $seed_keys = ['quantity','year', 'local', 'description', 'public', 'available', 'description',
      'latin_name','common_name','polinization','direct',
    ];
    $seed_taxonomy = ['species', 'variety','family'];
    $seed_new = [];
    $months_new = [];
    
    foreach ( $request->input() as $key =>  $value ){
      if (in_array($key, $seed_keys)){
        $seed_new[$key] = $value;
      }
      if (in_array($key, $seed_taxonomy)){
        // TODO: Should do a special function to work this out
        if ($value) { 
          $t = (array)\DB::table($key)->where('name', $value)->first();
          if (! $t) {
            $t['id'] = \DB::table($key)->insertGetId(['name' => $value]);
          }

          $seed_new[$key . '_id'] = $t['id'];
          //$seed_new[$key] = $t;
          //unset($seed_new[$key]);
        }
      }
      if ($key == 'months'){
        ///dd($value);
        $months_new = $value;
      }
    }

    if ($request->input('_id')){
      $seed_id = $request->input('_id');
      $seed = \Caravel\Seed::findOrFail($seed_id);
      if (Gate::denies('update-seed', $seed)){
        abort(403);
      }
      $seed->update($seed_new);
      $seed->syncMonths($months_new);
    } else {
      $seed_new['user_id'] = $request->user()->id;
      $seed = \Caravel\Seed::create($seed_new);
      foreach($months_new as $month){
        $seed->months()->save(new \Caravel\SeedMonth(['month'=> $month ]));
      }
      if ($request->input('pictures_id')){
        foreach($request->input('pictures_id') as $picture_id){
          $picture = \Caravel\Picture::findOrFail($picture_id);
          $seed->pictures()->save($picture);
        }
      }
      // maybe flash an 'Added new seed' message
    }

    return redirect('/seedbank/myseeds');
  }
  public function getPreferences()
  {
    $user = \Auth::user();
    $updatelocation = false;
    $location = false; 
    $locale = $user->locale;
	$geoipreader = new Reader(storage_path('app/geoip.mmdb'), array($locale, 'en'));
	try {
		$geoipdata = $geoipreader->city(request()->ip());
		$updatelocation = [ 'lat' => $geoipdata->location->latitude,
			'lon' => $geoipdata->location->longitude,
			'place_name' => $geoipdata->city->name ];
    }
    catch(\GeoIp2\Exception\AddressNotFoundException $e){
      if ($user->place_name){
         $location = true;
      } 
	  $updatelocation = [ 'lat' => 12.2,
	    'lon' => 121.1,
	    'place_name' => 'Porto' ];
    }
    return view('seedbank::preferences')
      ->with('messages', \Lang::get('authentication::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('user', $user)
      ->with('username', $user->name)
      ->with('updatelocation', $updatelocation)
      ->with('location', $location)
      ->with('active', ['profile' => true]);
  }
  public function postPreferences(Request  $request)
  {
    $user = \Auth::user();
	$validator = $this->prefValidator($request->all(), [],\Lang::get('auth::validation'));
    
	if($validator->fails())
	{
		$this->throwValidationException(
			$request, $validator
		);
	}
    if (!$request->input('password')){
      unset($request['password']);
    } else {
      $request['password'] = bcrypt($request->password);
    };

    $user = $user->update($request->all());
    //dd($user);
	return redirect('/seedbank');
  }
  public function getSearch()
  {
    $user = \Auth::user();
    return view('seedbank::search')
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['search' => true]);

  }
  public function postSearch(Request $request)
  {
    // $user = \Auth::user();
    $user = $request->user();
    $q = [];
    foreach($request->input() as $key => $value){
      if (in_array($key, ['common_name', 'latin_name']) && ($value)){
        $q[$key] = $value;
      }
    }
    if (! $q){ return [];}
    //dd($q);
    $query = \Caravel\Seed::query()
      ->where('public', true)
      ->where('available', true)
      ->where('user_id', '!=', $user->id);
    $query->where(function($qu) use ($q){

      foreach($q as $key => $value){
        $qu->orWhere($key, 'like', '%' . $value . '%');
      }
    });
    $results = $query->select('id', 'common_name', 'latin_name', 'user_id')->distinct()->get();
    /*$result = [];
    foreach($results as $i){
      $myarray = (array)$i;
      $result[] = $myarray;
    };*/
    return $results;
  }
  public function postAutocomplete(Request $request)
  {
    // $user = \Auth::user();
    $user = $request->user();
    $query_term = $request->input('query');
    $query_name = $request->input('query_name');
    if (! in_array($query_name, ['common_name', 'latin_name'])){
      return [];
    }
    $results = \DB::table('seeds')
      ->join('seeds_banks', 'seeds_banks.seed_id', '=', 'seeds.id')
      ->where($query_name, 'like', '%' . $query_term . '%')
      ->where(function($query) use ($user) { $query->where('public', true)->orWhere('user_id', $user->id);})
      ->select('seed_id', $query_name)->distinct()
      ->get();
    $result = array();
    foreach($results as $i){
      $myarray = (array)$i;
      $myarray['value'] = $myarray[$query_name];
      $myarray['id'] = $myarray['seed_id'];

      $result[] = $myarray;
    };
    return $result;
  }
  /*public function postSeed(Request $request, $id = null)
  {
    $seed = \Caravel\Seed::find($id);
    return $seed;
  }*/

  public function postMessageReply(Request $request)
  {
    // if error with form
    $this->validate($request, [
      'body' => 'required',
      'message_id' => 'required',
    ]);
    $message_id = $request->input('message_id');
    $message = $request->user()->messages()->where('id', $message_id)->first();
    if (Gate::denies('reply-message', $message)){
      abort(403);
    }
    $reply = $message->reply(['body' => $request->input('body')]);
    $message->pivot->replied = true;
    $message->pivot->save();

    if ($reply){
      return ["response" => "Message sent"];
    }
    return false;



    // maybe flash an 'Added new seed' message
    //return redirect('/seedbank');
  }

  public function postMessageSend(Request $request)
  {
    // if error with form
    $this->validate($request, [
      'body' => 'required',
      'seed_id' => 'required',
    ]);
    $subject = $request->input('subject');
    $body = $request->input('body');
    $seed_id = $request->input('seed_id');
    $seed = \Caravel\Seed::findOrFail($seed_id);
    $user_id = $seed->user_id;
    if (!$subject)
    {
      $subject = $seed->common_name;
    }
    $message = \Caravel\Message::create(
      [
        'user_id' => $request->user()->id,
          'subject' => $subject,
          'body' => $body,
        ]
      );
    $message->save();
    $message->root_message_id = $message->id;
    $request->user()->transactionStart(['asked_to'=>$user_id, 'seed_id'=>$seed_id]);

    // maybe flash an 'Added new seed' message
    return redirect('/seedbank/search');
  }
  public function postAddPicture (Request $request) {
			// TODO: Limit number of picture by seed?
			$user = \Auth::user();
			if ($request->has('seed_id')){
				$seed = \Caravel\Seed::findOrFail($request->input('seed_id'));
			} else {
				$seed = false;
			}
			if ($request->hasFile('pictures')) {
				$picture = $request->file('pictures')[0];
				$status = SeedBankController::save_image($picture);
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
		}
}
