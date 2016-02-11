<?php namespace Caravel\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($this->auth->guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized.', 401);
			}
			else
			{
				return redirect()->guest('auth/login');
			}
		}

		if (isset($this->auth->user()->locale)){
			$locale = $this->auth->user()->locale | config('app.locale');
			//$locale="en";
		} else {
			$locale = config('app.locale');
		}
	    \App::setLocale($locale);
        \View::share('lang', [$locale => true]);
        \View::share('langString', $locale);

		return $next($request);
	}

}
