<?php

namespace Tests\Feature\Infrastructure;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ProductionAssetUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_forwarded_https_request_renders_secure_asset_urls(): void
    {
        Config::set('app.env', 'production');
        Config::set('app.url', 'https://example.test');

        $response = $this
            ->withServerVariables([
                'HTTP_HOST' => 'example.test',
                'HTTP_X_FORWARDED_PROTO' => 'https',
                'HTTP_X_FORWARDED_HOST' => 'example.test',
                'HTTP_X_FORWARDED_PORT' => '443',
            ])
            ->get('/');

        $response->assertOk();
        $response->assertSee('https://example.test/build/assets/', false);
        $response->assertDontSee('http://example.test/build/assets/', false);
    }
}
