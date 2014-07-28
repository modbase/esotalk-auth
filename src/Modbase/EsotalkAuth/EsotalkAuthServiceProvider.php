<?php namespace Modbase\EsotalkAuth;

use Illuminate\Support\ServiceProvider;

class EsotalkAuthServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['esotalk.config'] = $this->app->share(function($app)
		{
			$path = $app['config']['esotalk-auth::path'].'config/config.php';

			return new ConfigParser($path);
		});

		$this->app['esotalk.db.connector'] = $this->app->share(function($app)
		{
			$factory = $app['db.factory'];
			$configParser = $app['esotalk.config'];

			return new DatabaseConnector($factory, $configParser);
		});

		$this->app['esotalk.cookie.storage'] = $this->app->share(function($app)
		{
			$configParser = $app['esotalk.config'];

			return new CookieStorage($app['request'], $configParser);
		});

		// Register the Esotalk authentication driver
		$this->app->resolving('auth', function($auth)
		{
			$auth->extend('esotalk', function($app)
			{
				$connector = $app['esotalk.db.connector'];
				$configParser = $app['esotalk.config'];
				$provider = new UserProvider($connector->connection(), $configParser);

				return new Guard($provider, $app['esotalk.cookie.storage']);
			});
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('modbase/esotalk-auth');
	}

}
