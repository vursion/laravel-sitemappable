<?php

namespace Vursion\LaravelSitemappable;

trait IsSitemappable
{
	protected static function bootIsSitemappable()
	{
		static::saved(function ($model) {
			static::deleteModel($model, false);

			if ($model->shouldBeSitemappable()) {
				static::addModel($model);
			} else {
				static::deleteModel($model, true);
			}
		});

		static::deleted(function ($model) {
			static::deleteModel($model, true);
		});
	}

	protected static function addModel($model)
	{
		$sitemap = Sitemappable::withTrashed()->firstOrCreate([
			'entity_id'   => $model->id,
			'entity_type' => get_class($model),
		]);
		$sitemap->restore();
		$sitemap->urls = $model->toSitemappableArray();
		$sitemap->save();
	}

	protected static function deleteModel($model, $forceDelete = false)
	{
		$sitemap = Sitemappable::where('entity_type', get_class($model))
						->where('entity_id', $model->id)
						->withTrashed();

		if ($sitemap) {
			if ($forceDelete) {
				$sitemap->forceDelete();
			} else {
				$sitemap->delete();
			}
		}
	}

	/**
	 * Determine if the model should be sitemappable.
	 *
	 * @return bool
	 */
	public function shouldBeSitemappable()
	{
		return true;
	}

	/**
	 * Returns an array with the (localized) URLs.
	 *
	 * @return array
	 */
	public function toSitemappableArray()
	{
		return [];
	}
}
