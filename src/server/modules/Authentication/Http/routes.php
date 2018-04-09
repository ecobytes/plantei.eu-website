<?php
$languages = Config::get('app.availableLanguages');
$locale = \Request::segment(1);

if(in_array($locale, $languages)){
	\App::setLocale($locale);
	\View::share('langString', $locale);
}else{
	$locale = Config::get('app.locale');
	\App::setLocale($locale);
	\View::share('langString', $locale);
}

$lang[] = $locale;
foreach($languages as $l){
	if ($l != $locale){
		$lang[] = $l;
	}
}

\View::share('langs', $lang);

Route::group(['prefix' => 'auth', 'namespace' => 'Modules\Authentication\Http\Controllers'], function()
{
	Route::get('/logout', 'AuthController@getLogout')->name('logout');
	Route::group(['middleware' => 'csrf'], function(){
		Route::post('/register', 'AuthController@postRegister')->name('register');
		Route::post('/login', 'AuthController@postLogin')->name('login');
	});
});


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
