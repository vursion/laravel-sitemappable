{!! '<' . '?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
	@foreach ($sitemappables as $sitemappable)
		@foreach ($sitemappable->urls as $url)
			<url>
				<loc>{{ $url }}</loc>
				@if (count(array_keys($sitemappable->urls)) > 1)
					@foreach ($sitemappable->urls as $lang => $url)
						<xhtml:link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}"></xhtml:link>
					@endforeach
				@endif
				@if ($sitemappable->updated_at)
					<lastmod>{{ $sitemappable->updated_at->toIso8601String() }}</lastmod>
				@endif
			</url>
		@endforeach
	@endforeach
</urlset>
