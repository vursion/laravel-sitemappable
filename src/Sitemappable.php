<?php

namespace Vursion\LaravelSitemappable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sitemappable extends Model
{
	use SoftDeletes;

	public $guarded  = [];

	protected $casts = [
		'urls' => 'array',
	];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$this->table = config('sitemappable.db_table_name');
	}
}
