<?php namespace Modules\Authentication\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Http\Request;
use Pingpong\Modules\Routing\Controller;

use \Caravel\User;

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
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;
		$this->middleware('guest', ['except' => ['getLogout', 'getSettings']]);
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
			->with('hideMenu', true)
			->with('csrfToken', csrf_token())
			->with('develUserExists', $develUserExists);

	}

	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'name' => 'required', 'password' => 'required',
		]);

		$credentials = $request->only('name', 'password');
      if(filter_var($credentials['name'], FILTER_VALIDATE_EMAIL)) {
          $credentials['email'] = $credentials['name'];
		  unset($credentials['name']);
      }
      /* else {
          $credentials['name'] = $username;
      }
      if (Auth::once($credentials)) {
          return Auth::user()->id;
	  }*/

		$credentials['confirmed'] = 1;
		if ($this->auth->attempt($credentials, $request->has('remember')))
		{
			return redirect()->intended($this->redirectPath());
		}

		return redirect($this->loginPath())
					->withInput($request->only('name', 'remember'))
					->withErrors([
						'email' => 'These credentials do not match our records or account not active.',
					]);
	}
	public function getLogout()
	{
		//\Auth::logout();
		$this->auth->logout();
		return redirect('/');
	}

	public function getRegister()
	{
		$errors = \Session::get('errors');
		$oldInput = [];
		if(\Session::hasOldInput()){
			$oldInput =  \Session::getOldInput();
			if(isset($oldInput['subscribeNewsletter'])){
				$k = $oldInput['subscribeNewsletter'];
				$oldInput['subscribeNewsletter'] = [];
				$oldInput['subscribeNewsletter'][$k] = true;
			}else{
				$oldInput['subscribeNewsletter'][1] = true;
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
		$validator = $this->registrar->validator($request->all(), [],\Lang::get('auth::validation'));

		if($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}
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

		$user->push();
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
		->with('title', 'Registration Successful')
		->with('message', \Lang::get('auth::messages.subscriptionSuccessfulMessage'))
		->with('buttons', array(['label' => \Lang::get('newsletter::messages.homePage'), 'url' => '/']));

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
			$title = \Lang::get('auth::messages.successConfirmingTitle');
			$message =  \Lang::get('auth::messages.successConfirmingMessage');
		}else {
			$success = false;
			$title = \Lang::get('auth::messages.errorConfirmingTitle');
			$message =  \Lang::get('auth::messages.errorConfirmingMessage');

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
