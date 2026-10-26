<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root URL now redirects to the billing page.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertRedirect('/billing');
    }
}
