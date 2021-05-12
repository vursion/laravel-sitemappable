<?php

namespace Vursion\LaravelSitemappable\Tests;

use Illuminate\Database\Eloquent\Model;
use Vursion\LaravelSitemappable\IsSitemappable;

class TestModel extends Model
{
	use isSitemappable;

	protected $table = 'test_models';

	protected $guarded = [];

	public function toSitemappableArray()
	{
		return [
			'nl' => 'https://www.vursion.io/nl/testen/test-slug-in-het-nederlands',
			'en' => 'https://www.vursion.io/en/tests/test-slug-in-english',
		];
	}

	public function shouldBeSitemappable()
	{
		return (! $this->draft && $this->published);
	}
}
