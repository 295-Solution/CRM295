<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_route_is_rate_limited(): void
    {
        $user = User::factory()->create([
            'email' => 'ratelimit@example.test',
            'password' => 'correct-password-123',
        ]);

        for ($i = 0; $i < 20; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

            $response->assertStatus(302);
        }

        $throttled = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $throttled->assertStatus(429);
    }

    public function test_api_write_route_is_rate_limited_for_sales_user(): void
    {
        $sales = User::factory()->create([
            'role' => 'sales',
        ]);

        Sanctum::actingAs($sales);

        for ($i = 1; $i <= 60; $i++) {
            $response = $this->postJson('/api/leads', [
                'nama_client' => 'Lead '.$i,
                'perusahaan' => 'PT Rate Limit',
                'no_hp' => '0812000'.$i,
                'email' => "lead{$i}@example.test",
                'alamat' => 'Jakarta',
                'sumber_lead' => 'website',
                'status' => 'Cold',
                'assigned_to' => $sales->id,
                'notes' => null,
            ]);

            $response->assertCreated();
        }

        $throttled = $this->postJson('/api/leads', [
            'nama_client' => 'Lead 61',
            'perusahaan' => 'PT Rate Limit',
            'no_hp' => '081200061',
            'email' => 'lead61@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'Cold',
            'assigned_to' => $sales->id,
            'notes' => null,
        ]);

        $throttled->assertStatus(429);
    }
}
