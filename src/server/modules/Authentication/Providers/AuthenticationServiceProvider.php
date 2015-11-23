<?php namespace Modules\Authentication\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class AuthenticationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot the application events.
	 *
	 * @return void
	 */
	public function boot(GateContract $gate)
	{
		\Lang::addNamespace('auth', dirname (__DIR__).'/Resources/lang');
		\View::addNamespace('auth', dirname (__DIR__).'/Resources/views');

		$this->registerConfig();
		$this->registerTranslations();
		$this->registerViews();
		$this->registerPolicies($gate);

        $gate->define('update-seeds_bank', function ($user, $seeds_bank) {
            return $user->id == $seeds_bank->user_id;
		});
        $gate->define('reply-message', function ($user, $message) {
			/*echo $user->id;
			dd($message);*/
			if ($user->messages()->where('id', $message->id)->get()->toArray())
			{
				return true;
			}
			return false;
		});

		
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Register config.
	 *
	 * @return void
	 */
	protected function registerConfig()
	{
		$this->publishes([
		    __DIR__.'/../Config/config.php' => config_path('authentication.php'),
		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'authentication'
		);
	}

	/**
	 * Register views.
	 *
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = base_path('views/modules/authentication');

		$sourcePath = __DIR__.'/../Resources/views';

		$this->publishes([
			$sourcePath => $viewPath
		]);

		$this->loadViewsFrom([$viewPath, $sourcePath], 'authentication');
	}

	/**
	 * Register translations.
	 *
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = base_path('resources/lang/modules/authentication');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'authentication');
		} else {
			$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'authentication');
		}
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
