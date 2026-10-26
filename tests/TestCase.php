<?php

namespace Tests;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\RateLimiter;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable rate limiting for tests so that running many requests in
        // a single test suite doesn't trigger HTTP 429 responses.
        RateLimiter::for('api', fn () => Limit::none());
    }
}
