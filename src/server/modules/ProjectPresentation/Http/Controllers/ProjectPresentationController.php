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
		$login = \Session::get('login');
		$formErrors = '';
		if(!empty($errors)){
			$formErrors = $errors;
			\View::share('errors', $errors->default->toArray());
	  }

		$modal_content = join("\n", [
			view('auth::login_modal')->with('formErrors', $formErrors)
			  ->with('login', $login)
			  ->with('messages', \Lang::get('auth::messages'))
				->with('csrfToken', csrf_token())->render(),
			view('auth::register_modal')->with('formErrors', $formErrors)
			  ->with('login', $login)
			  ->with('messages', \Lang::get('auth::messages'))
				->with('csrfToken', csrf_token())->render(),
		]);


		return view('projectpresentation::index')
		->with('messages', \Lang::get('projectpresentation::messages'))
		->with('showSubscription', $showSubscription)
		->with('showAuthentication', $showAuthentication)
		->with('bodyId', 'index')
		->with('modal', true)
		->with('formErrors', $formErrors)
		->with('modal_content', $modal_content)
		->with('csrfToken', csrf_token());
	}
}
