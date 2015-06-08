<?php namespace Modules\Newsletter\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use \Modules\Newsletter\Http\Requests\NewsletterRequest;

class NewsletterController extends Controller {
	
	/*public function index()
	{
			\App::setLocale('en');

		return view('newsletter::emails.confirmation')->with('data', \Lang::get('newsletter::confirmationemail'));
	}*/

	public function subscribed(){
		return view('newsletter::subscribed')
		->with('title', \Lang::get('newsletter::messages.subscriptionSuccessfulTitle'))
		->with('message', \Lang::get('newsletter::messages.subscriptionSuccessfulMessage'))
		->with('buttons', array(['label' => \Lang::get('newsletter::messages.homePage'), 'url' => '/']));
	}

	public function store(NewsletterRequest $request){
		$verificationKey = substr(sha1(rand()), 0, 32);
		$subscriptor = new \Modules\Newsletter\Entities\NewsletterSubscriptor(\Input::all());
		$subscriptor->verified = false;
		$subscriptor->verification_key = $verificationKey;
		$subscriptor->prefered_language = \App::getLocale();
		$subscriptor->active = false;
		$subscriptor->save();
		\Mail::send('newsletter::emails.confirmation', [
			'verificationKey' => $verificationKey, 
			'baseUrl' => \URL::to('/'),
			'activeLang' => \App::getLocale(),
			'siteName' => env('SITE_NAME'),
			'data' => array(
				'title' => \Lang::get('newsletter::confirmationemail.title'),
				'text' => \Lang::get('newsletter::confirmationemail.text'),
				'buttonText' => \Lang::get('newsletter::confirmationemail.buttonText')
				)

], function($message)
		{
    	$message->to(\Request::input('email'), \Request::input('name'))->subject(\Lang::get('newsletter::confirmationemail.title'));
		});
		return \Redirect::to('/'.\App::getLocale().'/newsletter/subscribed');
	}

	public function update($email = null){
		if(is_null($email)){
			return ;
		}
		$key = \Input::get('verification_key');
		$subscriptor = \Modules\Newsletter\Entities\NewsletterSubscriptor::where('email', $email)->where('verification_key', $key)->firstOrFail();
		$subscriptor->prefered_language = \Input::get('prefered_language');
		$subscriptor->save();
		\App::setLocale(\Input::get('prefered_language'));
		return view('fullPageMessage', array(
			'title' =>  \Lang::get('newsletter::messages.successChangingSettingsTitle'),
			'message' => null,
			'buttons' => array(['label' => \Lang::get('newsletter::messages.homePage'), 'url' => '/'.\Input::get('prefered_language')])
			));
		
	}

	public function confirm($key){

		$subscriptor = \Modules\Newsletter\Entities\NewsletterSubscriptor::where('verification_key', '=', $key)->first();
		if(!$subscriptor){
			return view('fullPageMessage', array(
			'title' =>  \Lang::get('newsletter::messages.errorConfirmingEmailTitle'),
			'message' => \Lang::get('newsletter::messages.errorConfirmingEmailMessage'),
			'buttons' => array(['label' => \Lang::get('newsletter::messages.homePage'), 'url' => '/'])
			));
		}
		$subscriptor->verified = true;
		$subscriptor->active = true;
		$subscriptor->save();

		$changeToLanguages = \Config::get('app.availableLanguagesFull');
		//unset($changeToLanguages[\App::getLocale()]);

		return view('newsletter::confirmed', array(
			'title' =>  \Lang::get('newsletter::messages.successConfirmingEmailTitle'),
			'message' => '<p>'.\Lang::get('newsletter::messages.successConfirmingEmailMessage').
				'</p>',
			'changeLangsMessage' => '<p>'.
				\Lang::get('newsletter::messages.changeNewsletterLanguageQuestion', ['lang' => \Config::get('app.availableLanguagesFull.'.\App::getLocale())]).'</p>'. \Lang::get('newsletter::messages.changeNewsletterLanguageCallToAction'),
			'changeToLanguages' => $changeToLanguages,
			'selectedLanguage' => \Config::get('app.availableLanguagesFull.'.\App::getLocale()),
				'buttons' => array(['label' => \Lang::get('newsletter::messages.homePage'), 'url' => '/']),
			'csrfToken' => csrf_token(),
			'email' => $subscriptor->email,
			'verificationKey' => $key

		));
	}
}