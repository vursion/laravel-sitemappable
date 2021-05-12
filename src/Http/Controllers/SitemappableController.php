<?php

namespace Vursion\LaravelSitemappable\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Vursion\LaravelSitemappable\Sitemappable;

class SitemappableController extends Controller
{
	public function index()
	{
		$content = Cache::remember('sitemap.xml', \DateInterval::createFromDateString(config('sitemappable.cache')), function () {
			$otherRoutes = collect($this->otherRoutes())->map(function ($route) {
				return new Sitemappable([
					'urls' => $route,
				]);
			});

			$sitemappables = Sitemappable::get()->concat($otherRoutes)->filter(function ($sitemappable) {
				return (is_array($sitemappable->urls) && count($sitemappable->urls) > 0);
			});

			return view('sitemappable::sitemap', compact('sitemappables'))->render();
		});

		return response(preg_replace('/>(\s)+</m', '><', $content), '200')->header('Content-Type', 'text/xml');
	}

	protected function otherRoutes()
	{
		return [];
	}
}
