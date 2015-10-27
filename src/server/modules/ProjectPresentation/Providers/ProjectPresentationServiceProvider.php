<?php namespace Modules\Projectpresentation\Providers;

use Illuminate\Support\ServiceProvider;

class ProjectPresentationServiceProvider extends ServiceProvider {

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

		\Lang::addNamespace('projectpresentation', dirname (__DIR__).'/Resources/lang');

		\View::addNamespace('projectpresentation', dirname (__DIR__).'/Resources/views');
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
		    __DIR__.'/../Config/config.php' => config_path('projectpresentation.php'),
		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'projectpresentation'
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
