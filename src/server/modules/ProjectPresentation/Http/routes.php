<?php
$languages = Config::get('app.availableLanguages');
	$locale = Request::segment(1);
if(in_array($locale, $languages)){
	\App::setLocale($locale);
	\View::share('lang', [$locale => true]);
	\View::share('langString', $locale);
}else{
	$locale = null;
	\View::share('lang', ['pt' => true]);
	\View::share('langString', 'pt');

}

Route::group(array('prefix' => $locale), function(){

	Route::group(['namespace' => 'Modules\ProjectPresentation\Http\Controllers'], function()
	{
		Route::get('/', 'ProjectPresentationController@index');
	});

});
