<?php

Route::get('sitemap.xml', [config('sitemappable.controller'), 'index']);
