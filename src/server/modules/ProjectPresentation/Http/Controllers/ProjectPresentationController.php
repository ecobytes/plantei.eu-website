<?php namespace Modules\Projectpresentation\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class ProjectPresentationController extends Controller {
	
	public function index()
	{
		$showSubscription = false;
		$enabledModules = \Module::enabled();
		if(isset($enabledModules['Newsletter'])){
			$showSubscription = true;
		}
		$errors = \Session::get('errors');
		if(!empty($errors)){
		\View::share('errors', $errors->default->toArray());
		}
		return view('projectpresentation::index')->with('showSubscription', $showSubscription);
		->with('csrfToken', csrf_token());
	}
	
}