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

	Route::group(['prefix' => 'newsletter', 'namespace' => 'Modules\Newsletter\Http\Controllers'], function()
	{
		//Route::get('/', 'NewsletterController@index');
		
		Route::group(['middleware' => 'csrf'], function(){
			Route::post('/', 'NewsletterController@store');
			Route::patch('{email}', 'NewsletterController@update');
		});
		Route::get('/subscribed', 'NewsletterController@subscribed');
		Route::get('confirm/{key}', 'NewsletterController@confirm');
	});


});