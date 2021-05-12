{!! '<' . '?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
	@foreach ($sitemappables as $sitemappable)
		<url>
			<loc>{{ $sitemappable->urls[array_key_first($sitemappable->urls)] }}</loc>
			@foreach ($sitemappable->urls as $lang => $url)
				<xhtml:link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}"></xhtml:link>
			@endforeach
			@if ($sitemappable->updated_at)
				<lastmod>{{ $sitemappable->updated_at->toIso8601String() }}</lastmod>
			@endif
		</url>
	@endforeach
</urlset>
