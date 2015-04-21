<?php namespace Dakshhmehta\EmailManager;

use Illuminate\Support\ServiceProvider;

class EmailManagerServiceProvider extends ServiceProvider {

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
		$this->package('dakshhmehta/email-manager');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Bindings
		$this->app->bind('Dakshhmehta\EmailManager\Repositories\EmailTemplateRepository', 'Dakshhmehta\EmailManager\EmailTemplateProvider');
		$this->app->bind('Dakshhmehta\EmailManager\Repositories\EmailRepository', 'Dakshhmehta\EmailManager\EmailProvider');

		$this->app['email'] = $this->app->share(function() {
			return $this->app->make('Dakshhmehta\EmailManager\Repositories\EmailRepository');
		});

		$this->app['email.template'] = $this->app->share(function(){
			return $this->app->make('Dakshhmehta\EmailManager\Repositories\EmailTemplateRepository');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('email', 'email.template');
	}

}
