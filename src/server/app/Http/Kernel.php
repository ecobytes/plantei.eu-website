<?php namespace Caravel\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's global HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		//'Caravel\Http\Middleware\VerifyCsrfToken',
		'LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware',
		'Caravel\Http\Middleware\GlobalVars'
	];

	/**
	 * The application's route middleware.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => 'Caravel\Http\Middleware\Authenticate',
		'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
		'guest' => '\Caravel\Http\Middleware\RedirectIfAuthenticated',
		'csrf' => '\Caravel\Http\Middleware\VerifyCsrfToken',
	];

}
