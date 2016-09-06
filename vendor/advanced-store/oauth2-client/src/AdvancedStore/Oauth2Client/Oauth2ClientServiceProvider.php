<?php

/**
 * @package OAuth2 Client
 * @author  advanced STORE GmbH
 * Date:    03.04.14
 */

namespace AdvancedStore\Oauth2Client;

use Illuminate\Support\ServiceProvider;

class Oauth2ClientServiceProvider extends ServiceProvider {

	const PACKAGE_NAME = 'oauth2-client';

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../config/oAuth2ClientConfig.php' => config_path('oAuth2ClientConfig.php')
		]);


	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerOauth2Client();
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

	protected function registerOauth2Client() {

		$this->app->bindShared('asOauth2Client',function($app) {

			$config = $app['config'];

			return new Oauth2Client( $config->get('oAuth2ClientConfig.client.id'), $config->get('oAuth2ClientConfig.client.secret') );

		});

	}

}
