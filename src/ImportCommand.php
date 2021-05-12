<?php

namespace Vursion\LaravelSitemappable;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Vursion\LaravelSitemappable\Sitemappable;

class ImportCommand extends Command
{
	protected $signature = 'sitemappable:import';

	protected $description = 'Rebuild the sitemap from scratch';

	protected $results;

	public function handle()
	{
		DB::table(config('sitemappable.db_table_name'))->truncate();

		$this->process();

		$this->table(['Model', 'Result'], $this->results);
	}

	public function process()
	{
		$this->results = $this->fetchCandidates()->filter(function ($class) {
			return class_exists($class);
		})->map(function ($class) {
			$reflection = new ReflectionClass($class);

			if (! $reflection->isAbstract() && $reflection->isSubclassOf(\Illuminate\Database\Eloquent\Model::class)){
				if (in_array('Vursion\LaravelSitemappable\IsSitemappable', $reflection->getTraitNames())) {
					return [
						'<info>' . $class . '</info>',
						'<info>' . $this->import($class) . '</info>'
					];
				}

				return [
					'<comment>' . $class . '</comment>',
					'<comment>Sitemappable trait not found</comment>',
				];
			}
		})->filter()->toArray();
	}

	protected function fetchCandidates()
	{
		return collect(File::allFiles(base_path(config('sitemappable.model_directory'))))->filter(function ($file) {
			return ($file->getExtension() === 'php');
		})->map(function ($file) {
			return str_replace([base_path(), '/', '\app\\', '.php'], ['', '\\', 'App\\', ''], $file->getRealPath());
		});
	}

	protected function import($class)
	{
		$records = $class::get()->each(function ($model) use ($class) {
			if ($model->shouldBeSitemappable()) {
				Sitemappable::create([
					'entity_id'   => $model->id,
					'entity_type' => $class,
					'urls'        => $model->toSitemappableArray(),
				]);
			}
		});

		return trans_choice('{0} 0 records processed|{1} 1 record processed|[2,*] :records records processed', $records->count(), ['records' => $records->count()]);
	}
}
