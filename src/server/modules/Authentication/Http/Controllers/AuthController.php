<?php namespace Modules\Authentication\Http\Controllers;

use Validator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Http\Request;
use Pingpong\Modules\Routing\Controller;

use \Caravel\User;

use GeoIp2\Database\Reader;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 *
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;
		$this->middleware('guest', ['except' => ['getLogout', 'getSettings']]);
	}
	 */
	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest', ['except' => ['getLogout', 'getSettings']]);
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		if (!$data['email']){unset($data['email']);};
		return Validator::make($data, [
			'name' => 'required|max:255|unique:users',
			'email' => 'sometimes|required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
			'lon' => 'required_with:lat|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/',
			'lat' => 'required_with:lon|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/',
			'place_name' => 'max:255|required_with:lon,lat',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return Caravel\User
	 */
	public function create(array $data)
	{
		if ($data['saveLocation'] == "0")  {
			$data['place_name'] = false; $data['lon'] = false; $data['lat'] = false; 
		}
		unset($data['saveLocation']);

		foreach ($data as $key => $value){
			if ((!$value) || (!in_array($key, ['name', 'email', 'password', 'place_name', 'lat', 'lon']))){
				unset($data[$key]);
			}
		}
		$data['password'] = bcrypt($data['password']);
		return User::create($data);
	}

	public function getLogin()
	{
		$develUserExists = false;
		if($_ENV['APP_ENV'] == 'local'){
			$testUserCount = \Caravel\User::where('email', 'devel@example.com')->count();
			if($testUserCount == 1);
			$develUserExists = true;
		}
		return view('auth::login')
			->with('messages', \Lang::get('auth::messages'))
			->with('hideMenu', true)
			->with('csrfToken', csrf_token())
			->with('develUserExists', $develUserExists);

	}

	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'nameoremail' => 'required', 'password' => 'required',
		]);

		$credentials = $request->only('nameoremail', 'password');
      		if(filter_var($credentials['nameoremail'], FILTER_VALIDATE_EMAIL)) {
          		$credentials['email'] = $credentials['nameoremail'];
			} else {
				$credentials['name'] = $credentials['nameoremail'];
			}
		  	unset($credentials['nameoremail']);

		$credentials['confirmed'] = 1;
		if (\Auth::attempt($credentials, $request->has('remember')))
		{
			return redirect()->intended($this->redirectPath());
		}

		return redirect($this->loginPath())
		  ->withInput($request->only('nameoremail', 'remember'))
		  ->withErrors([
		    'email' => 'These credentials do not match our records or account not active.',
		  ]);
	} 
	public function getLogout()
	{
		\Auth::logout();
		return redirect('/');
	}

	public function getRegister()
	{
		$errors = \Session::get('errors');
		$oldInput = [];
		$geoipinfo = [];
		if(\Session::hasOldInput()){
			$oldInput =  \Session::getOldInput();
			if(isset($oldInput['subscribeNewsletter'])){
				$k = $oldInput['subscribeNewsletter'];
				$oldInput['subscribeNewsletter'] = [];
				$oldInput['subscribeNewsletter'][$k] = true;
			}else{
				$oldInput['subscribeNewsletter'][1] = true;
			}

			if ($oldInput['saveLocation'] == "0") {
				$oldInput['saveLocation'] = false;
			} else {
				$oldInput['saveLocation'] = true ;

			}

		} else {
			$locale = 'pt';
			if ($locale == 'pt'){
				$preflocale = array('pt', 'pt-BR', 'en');
			}
		    $geoipreader = new Reader(config('geoip.maxmind.database_path'), $preflocale);
			try {
				$geoipdata = $geoipreader->city(request()->ip());
				$oldInput = [ 'lat' => $geoipdata->location->latitude,
					'lon' => $geoipdata->location->longitude,
					'place_name' => $geoipdata->city->name ];
			}
      catch(\GeoIp2\Exception\AddressNotFoundException $e){
        $oldInput = [];
        if ( config('app.debug') ) {
			  	$oldInput = [ 'lat' => "123.21",
			  		'lon' => "33.3",
			  		'place_name' => "LISABON" ];
			  }
      }
		}

		//Subscribe newsletter defaults to true
		if(!isset($oldInput['subscribeNewsletter'])){
			$oldInput['subscribeNewsletter'][1] = true;
		}
		if(!empty($errors)){
		\View::share('errors', $errors->default->toArray());
		}

		return view('auth::register')
		->with('messages', \Lang::get('auth::messages'))
		->with('csrfToken', csrf_token())
		->with('oldInput', $oldInput);
	}

	public function postRegister(Request $request)
	{
		/*$this->validate($request, [
			'name' => 'required', 
                        'password' => 'required', 
                        'password_confirmation' => 'required|same:password',
			'email' => 'email'
		]);*/
		$validator = $this->validator($request->all(), [],\Lang::get('auth::validation'));
		//dd($validator->fails());
                /*
		//$validator = $this->registrar->validator($request->all(), [],\Lang::get('auth::validation'));
				 */
		if($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}/*
		$user = $this->registrar->create($request->all());
		$user->confirmationString = substr(sha1(rand()), 0, 32);
		$user->confirmed = true;
		if(User::count() == 1){
			$adminId = \Caravel\Role::where('name', 'admin')->first()->id;
			$user->roles()->attach($adminId);
		}else{
			$userId = \Caravel\Role::where('name', 'user')->first()->id;
			$user->roles()->attach($userId);
		}
		 */


		$user= $this->create($request->all());
	//	dd($user);
		$adminId = \Caravel\Role::where('name', 'admin')->first()->id;
		$user->confirmed = true;
		if(User::count() == 1){
			$adminId = \Caravel\Role::where('name', 'admin')->first()->id;
			$user->roles()->attach($adminId);
		}else{
			$userId = \Caravel\Role::where('name', 'user')->first()->id;
			$user->roles()->attach($userId);
			$user->roles()->attach($adminId);
		}


		$user->save();
			//push();
    // TODO: Cleanup messages
    $thread = \Cmgmyr\Messenger\Models\Thread::create([
			'subject' => \Lang::get('auth::confirmationemail.title'),
    ]);
    \Cmgmyr\Messenger\Models\Message::create([
      'thread_id' => $thread->id,
			'body' => \Lang::get('auth::confirmationemail.text'), 
			'user_id' => 1,
    ]);
    $thread->addParticipants([1, $user->id]);
    /*\Cmgmyr\Messenger\Models\Participant::create([
      'thread_id' => $thread->id,
      'user_id'   => $user->id,
      'last_read' => new \Carbon\Carbon,
    ]);*/

		if (!$thread){ dd("Error Creating Message");};

		// DEBUG:TEST:TODO: Initiate transactions, to and from user
		//                : Create one seed

		/*$faker = \Faker\Factory::create();
		$seed_id =  random_int(1,10);
		$seed_initial = \Caravel\Seed::firstOrCreate([
			'local' => 'teste-' . $faker->city,
			'year' => random_int(2010,2015),
			'description' => "Semente para testar plataforma:\n" . $faker->text(500),
			'available' => true,
			'public' => true,
			'user_id' => $user->id
		]);
		$seed = false;
		while (!$seed){
			$seed = \Caravel\Seed::find(random_int(1,20));
			if ($seed){
				if ($seed->user_id == $user->id){ $seed = false;}
			}
		}
		\Caravel\User::find(1)
			->startTransaction(['asked_to'=>$user->id, 'seed_id'=>$seed_initial->id]);
    $user->startTransaction(['asked_to'=>$seed->user_id, 'seed_id'=>$seed->id]);
     */



/*
		$verificationKey = $user->confirmationString;
		\Mail::send('emails.transactional', [
			'activeLang' => \App::getLocale(),
			'siteName' => env('SITE_NAME'),
			'data' => array(
				'title' => \Lang::get('auth::confirmationemail.title'),
				'text' => \Lang::get('auth::confirmationemail.text'),
				'buttonText' => \Lang::get('auth::confirmationemail.buttonText'),
				'buttonLink' => \URL::to('/').'/'.\App::getLocale().'/auth/confirm/'.$verificationKey
				)

], function($message)
		{
    	$message->to(\Request::input('email'), \Request::input('name'))->subject(\Lang::get('newsletter::confirmationemail.title'));
		});
 */
		return View('auth::successful-registration')
		->with('title', \Lang::get("auth::messages.registrationSuccessfulTitle"))
		//->with('message', \Lang::get("auth::messages.subscriptionSuccessfulMessage")
		->with('buttons', array(['label' => \Lang::get("auth::messages.homePage"), 'url' => '/']));

	}

	public function redirectPath()
	{
		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/seedbank';
		if (property_exists($this, 'redirectPath'))
		{
			return $this->redirectPath;
		}

		//return property_exists($this, 'redirectTo') ? $this->redirectTo : '/admin';
		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/seedbank';
	}

	public function getConfirm($confirmationString){
		$user = User::where('confirmationString', $confirmationString)->first();
		$success = null;
		if(!is_null($user)){
			$user->confirmed = true;
			$user->save();
			$success = true;
			$title = \Lang::get("auth::messages.successConfirmingTitle");
			$message =  \Lang::get("auth::messages.successConfirmingMessage");
		}else {
			$success = false;
			$title = \Lang::get("auth::messages.errorConfirmingTitle");
			$message =  \Lang::get("auth::messages.errorConfirmingMessage");

		}

		return View('auth::confirmed')->with('message', $message)->with('success', $success)->with('title', $title);
	}

	public function getSettings(){
		return View('auth.settings');
	}

	public function postSettings(){
		$validator = $this->registrar->validator($request->all());

		if ($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}
		debug($request->all());
	}

}
