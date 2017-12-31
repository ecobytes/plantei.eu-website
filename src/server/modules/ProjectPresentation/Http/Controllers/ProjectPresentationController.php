<?php namespace Modules\Projectpresentation\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class ProjectPresentationController extends Controller {

	public function index()
	{
    if ( ! \Auth::guest() ){
      return redirect('/seedbank');
    }
		$showSubscription = false;
		$showAuthentication = false;
		$enabledModules = \Module::enabled();
		if(isset($enabledModules['Newsletter'])){
			$showSubscription = true;
		}
		if(isset($enabledModules['Authentication'])){
			$showAuthentication = true;
		}
		$errors = \Session::get('errors');
		if(!empty($errors)){
		\View::share('errors', $errors->default->toArray());
		}
		return view('projectpresentation::index')
		->with('messages', \Lang::get('projectpresentation::messages'))
		->with('showSubscription', $showSubscription)
		->with('showAuthentication', $showAuthentication)
		->with('bodyId', 'index')
		->with('login', true)
		->with('csrfToken', csrf_token());
	}

}
