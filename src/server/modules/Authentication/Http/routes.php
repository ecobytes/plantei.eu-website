<?php
$languages = Config::get('app.availableLanguages');
$locale = \Request::segment(1);
if(in_array($locale, $languages)){
	\App::setLocale($locale);
	\View::share('lang', [$locale => true]);
	\View::share('langString', $locale);
}else{
	$locale = null;
	\View::share('lang', ['en' => true]);
	\View::share('langString', 'en');

}

Route::group(array('prefix' => $locale), function(){

	Route::group(['prefix' => 'auth', 'namespace' => 'Modules\Authentication\Http\Controllers'], function()
	{

		Route::group(['middleware' => 'csrf'], function(){
			Route::post('/register', 'AuthController@postRegister');
			Route::post('/login', 'AuthController@postLogin');
		});
		Route::get('/register', 'AuthController@getRegister');
		Route::get('/login', 'AuthController@getLogin');
		Route::get('/logout', 'AuthController@getLogout');
		//Route::get('/confirm/{key}', 'Authcontroller@getConfirm');
	});


});
