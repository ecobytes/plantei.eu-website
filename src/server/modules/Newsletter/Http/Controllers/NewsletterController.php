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
		->with('title', 'Inscrição bem sucedida')
		->with('message', 'Foi enviado um e-mail de confirmação para a sua caixa de email.')
		->with('buttons', array(['label' => 'Página Inicial', 'url' => '/']));
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
			'title' => 'Erro na Confirmação da Inscrição',
			'message' => 'Caso necessite de assistência técnica contacte '. env('TECH_SUPPORT_EMAIL') .'.',
			'buttons' => array(['label' => 'Página Inicial', 'url' => '/'])
			));
		}
		$subscriptor->verified = true;
		$subscriptor->active = true;
		$subscriptor->save();
		return view('fullPageMessage', array(
			'title' => 'Inscrição Confirmada',
			'message' => 'Está agora inscrito na nossa Newsletter.',
			'buttons' => array(['label' => 'Página Inicial', 'url' => '/'])
		));
	}
}