<?php namespace Caravel\Http\Middleware;

use Closure;

class GlobalVars {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$isProduction = false;
		if(env('APP_ENV') == 'production'){
			$isProduction = true;
		}
		 \View::share('isProduction', $isProduction);
		 \View::share('siteName', env('SITE_NAME'));
		 \View::share('footerText', \Lang::get('footer.text'));

		return $next($request);
	}

}
