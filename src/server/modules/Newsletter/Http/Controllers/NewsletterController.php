<?php namespace Modules\Newsletter\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use \Modules\Newsletter\Http\Requests\NewsletterRequest;

class NewsletterController extends Controller {
	
	/*public function index()
	{
		return view('newsletter::index');
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
		$subscriptor->active = false;
		$subscriptor->save();
		\Mail::send('newsletter::emails.confirmation', ['verificationKey' => $verificationKey, 'baseUrl' => \URL::to('/')
], function($message)
		{
    	$message->to(\Request::input('email'), \Request::input('name'))->subject('Confirmação de subscrição na Mailing list');
		});
		return \Redirect::to('/newsletter/subscribed');
	}

	public function confirm($key){
		$subscriptor = \Modules\Newsletter\Entities\NewsletterSubscriptor::where('verification_key', '=', $key)->first();
		if(!$subscriptor){
			return view('fullPageMessage', array(
			'title' =>  \Lang::get('newsletter::messages.errorConfirmingEmailTitle'),
			'message' => \Lang::get('newsletter::messages.errorConfirmingEmailMessage'),
			'buttons' => array(['label' => 'Página Inicial', 'url' => '/'])
			));
		}
		$subscriptor->verified = true;
		$subscriptor->active = true;
		$subscriptor->save();
		return view('fullPageMessage', array(
			'title' =>  \Lang::get('newsletter::messages.successConfirmingEmailTitle'),
			'message' => \Lang::get('newsletter::messages.successConfirmingEmailMessage'),
			'buttons' => array(['label' => 'Página Inicial', 'url' => '/'])
		));
	}
}