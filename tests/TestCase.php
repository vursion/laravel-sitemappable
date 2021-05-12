<?php

namespace Vursion\LaravelSitemappable\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Vursion\LaravelSitemappable\ImportCommand;
use Vursion\LaravelSitemappable\SitemappableServiceProvider;

abstract class TestCase extends Orchestra
{
    protected $mock;

	public function setUp(): void
	{
		parent::setUp();

		$this->setUpDatabase();
		$this->setUpTestModel();

		config()->set('sitemappable.cache', '0 minutes');
		config()->set('app.locale', 'nl');

		$this->mock = $this->createPartialMock(ImportCommand::class, ['fetchCandidates']);

		$this->mock->method('fetchCandidates')
                	->willReturn(collect(['Vursion\LaravelSitemappable\Tests\TestModel']));
	}

	protected function getPackageProviders($app)
	{
		return [SitemappableServiceProvider::class];
	}

	protected function setUpDatabase()
	{
		include_once __DIR__ . '/../database/migrations/create_sitemappable_table.php.stub';

		(new \CreateSitemappableTable())->up();
	}

	protected function setUpTestModel()
	{
		Schema::create('test_models', function (Blueprint $table) {
			$table->increments('id');
			$table->boolean('draft')->default(true);
			$table->boolean('published')->default(false);
			$table->timestamps();
		});
	}
}
