<?php

$languages = array('en','pt');
	$locale = Request::segment(1);
if(in_array($locale, $languages)){
	\App::setLocale($locale);
	\View::share('lang', [$locale => true]);
}else{
	$locale = null;
	\View::share('lang', ['en' => true]);

}

Route::group(array('prefix' => $locale), function(){

	Route::group(['namespace' => 'Modules\ProjectPresentation\Http\Controllers'], function()
	{
		Route::get('/', 'ProjectPresentationController@index');
	});

});