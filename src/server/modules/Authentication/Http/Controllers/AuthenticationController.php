<?php namespace Modules\Authentication\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class AuthenticationController extends Controller {
	
	public function index()
	{
		return view('authentication::index');
	}
	
}