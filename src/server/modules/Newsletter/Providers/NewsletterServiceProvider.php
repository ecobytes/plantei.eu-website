<?php namespace Modules\Newsletter\Providers;

use Illuminate\Support\ServiceProvider;

class NewsletterServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\Lang::addNamespace('newsletter', __DIR__.'/Resources/lang');
		\View::addNamespace('newsletter', __DIR__.'/Resources/views');
		$this->registerConfig();
	}

	/**
	 * Register config.
	 *
	 * @return void
	 */
	protected function registerConfig()
	{
		$this->publishes([
		    __DIR__.'/../Config/config.php' => config_path('newsletter.php'),
		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'newsletter'
		);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
