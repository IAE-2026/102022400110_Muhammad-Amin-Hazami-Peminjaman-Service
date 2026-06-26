<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JwtValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that accessing loans without X-IAE-KEY returns 401.
     */
    public function test_access_without_api_key_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/v1/loans');

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Unauthorized: Invalid or missing API Key',
                 ]);
    }

    /**
     * Test that accessing loans with valid X-IAE-KEY returns 200.
     */
    public function test_access_with_valid_api_key_returns_success(): void
    {
        $response = $this->withHeaders([
                            'X-IAE-KEY' => env('API_KEY', '102022400110'),
                         ])
                         ->getJson('/api/v1/loans');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data',
                 ]);
    }

    /**
     * Test that accessing loans with invalid X-IAE-KEY returns 401.
     */
    public function test_access_with_invalid_api_key_returns_unauthorized(): void
    {
        $response = $this->withHeaders([
                            'X-IAE-KEY' => 'wrong-key',
                         ])
                         ->getJson('/api/v1/loans');

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                 ]);
    }

    /**
     * Test that POST without body succeeds (returns 201) and conforms to wrapper.
     */
    public function test_post_without_body_succeeds_and_conforms_to_wrapper(): void
    {
        $response = $this->withHeaders([
                            'X-IAE-KEY' => env('API_KEY', '102022400110'),
                         ])
                         ->postJson('/api/v1/loans', []);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'loan' => [
                             'id',
                             'member_id',
                             'book_id',
                             'borrow_date',
                             'status',
                         ]
                     ],
                     'meta'
                 ])
                 ->assertJsonFragment([
                     'status' => 'success',
                 ]);
    }

}
