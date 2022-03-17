<?php

namespace Vursion\LaravelSitemappable;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Vursion\LaravelSitemappable\ImportCommand;

class SitemappableServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'sitemappable');
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');

		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/sitemappable.php' => config_path('sitemappable.php'),
			], 'config');

			$this->publishes([
            	__DIR__ . '/../database/migrations/create_sitemappable_table.php.stub' => $this->getMigrationFileName('create_sitemappable_table.php'),
       		], 'migrations');

			$this->publishes([
				__DIR__ . '/../src/Http/Controllers/SitemappableController.php.stub' => app_path('Http/Controllers/SitemappableController.php'),
			], 'controllers');
		}
	}

	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/sitemappable.php', 'sitemappable');

		$this->commands([
			ImportCommand::class,
		]);
	}

	protected function getMigrationFileName($migrationFileName)
    {
        return Collection::make(database_path('migrations/*'))
            ->flatMap(function ($path) use ($migrationFileName) {
                return $this->app->make(Filesystem::class)->glob($path . '*_' . $migrationFileName);
            })->push(database_path('migrations/' . date('Y_m_d_His') . '_' . $migrationFileName))->first();
    }
}
