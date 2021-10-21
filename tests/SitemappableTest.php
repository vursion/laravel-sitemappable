<?php

namespace Vursion\LaravelSitemappable\Tests;

use Illuminate\Support\Facades\DB;
use Vursion\LaravelSitemappable\SitemappableServiceProvider;

class SitemappableTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_can_be_instantiated()
    {
        static::assertInstanceOf('Vursion\LaravelSitemappable\SitemappableServiceProvider', $this->app->getProvider(SitemappableServiceProvider::class));
    }

    public function test_it_will_handle_a_should_be_sitemappable_model()
    {
        $testModel = TestModel::create([
            'draft'     => false,
            'published' => true,
        ]);

		$this->assertDatabaseHas('sitemap', [
		    'entity_id'   => $testModel->id,
			'entity_type' => get_class($testModel),
		]);

		$testModel->delete();
        $this->assertDatabaseMissing($testModel->getTable(), ['id' => $testModel->id]);

		$this->assertDatabaseMissing('sitemap', [
		    'entity_id'   => $testModel->id,
			'entity_type' => get_class($testModel),
		]);
    }

    public function test_it_will_discard_a_should_not_be_sitemappable_model()
    {
        $testModel = TestModel::create([
            'draft'     => false,
            'published' => false,
        ]);

        $this->assertDatabaseMissing('sitemap', [
            'entity_id'   => $testModel->id,
            'entity_type' => get_class($testModel),
        ]);
    }

    public function test_it_will_delete_a_no_longer_should_be_sitemappable_model()
    {
        $testModel = TestModel::create([
            'draft'     => false,
            'published' => true,
        ]);

        $this->assertDatabaseHas('sitemap', [
            'entity_id'   => $testModel->id,
            'entity_type' => get_class($testModel),
        ]);

        $testModel->published = false;
        $testModel->save();

        $this->assertDatabaseMissing('sitemap', [
            'entity_id'   => $testModel->id,
            'entity_type' => get_class($testModel),
        ]);
    }

    public function test_it_can_generate_an_xml_sitemap()
    {
        $testModel = TestModel::create([
            'draft'     => false,
            'published' => true,
        ]);

        $response = $this->get('sitemap.xml');

        $response->assertStatus(200);
        $this->assertContains('text/xml', explode(';', $response->headers->get('Content-Type')));

        $expected = preg_replace('/>(\s)+</m', '><', '<?xml version="1.0" encoding="UTF-8"?>
                        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
                            <url>
                                <loc>https://www.vursion.io/nl/testen/test-slug-in-het-nederlands</loc>
                                <xhtml:link rel="alternate" hreflang="nl" href="https://www.vursion.io/nl/testen/test-slug-in-het-nederlands"></xhtml:link>
                                <xhtml:link rel="alternate" hreflang="en" href="https://www.vursion.io/en/tests/test-slug-in-english"></xhtml:link>
                                <lastmod>' . $testModel->updated_at->toIso8601String() . '</lastmod>
                            </url>
                             <url>
                                <loc>https://www.vursion.io/en/tests/test-slug-in-english</loc>
                                <xhtml:link rel="alternate" hreflang="nl" href="https://www.vursion.io/nl/testen/test-slug-in-het-nederlands"></xhtml:link>
                                <xhtml:link rel="alternate" hreflang="en" href="https://www.vursion.io/en/tests/test-slug-in-english"></xhtml:link>
                                <lastmod>' . $testModel->updated_at->toIso8601String() . '</lastmod>
                            </url>
                        </urlset>');

        $this->assertXmlStringEqualsXmlString($expected, $response->getContent());
    }

    public function test_it_can_batch_import_existing_records()
    {
        $testModel = TestModel::create([
            'draft'     => false,
            'published' => true,
        ]);

        $skipTestModel = TestModel::create([
            'draft'     => false,
            'published' => false,
        ]);

        DB::table(config('sitemappable.db_table_name'))->truncate();

        $this->mock->process();

        $this->assertDatabaseHas('sitemap', [
            'entity_id'   => $testModel->id,
            'entity_type' => get_class($testModel),
        ]);

        $this->assertDatabaseMissing('sitemap', [
            'entity_id'   => $skipTestModel->id,
            'entity_type' => get_class($skipTestModel),
        ]);
    }
}
