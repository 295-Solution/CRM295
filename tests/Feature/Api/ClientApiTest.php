<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_authentication(): void
    {
        $this->getJson('/api/clients')->assertUnauthorized();
    }

    public function test_store_client_via_api(): void
    {
        $user = User::factory()->create([
            'role' => 'sales',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/clients', [
            'nama' => 'Api Client',
            'perusahaan' => 'PT API',
            'nomor_wa' => '081377788899',
            'sumber_client' => 'sosmed',
            'jenis_bisnis' => 'pendidikan',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.nama', 'Api Client');

        $this->assertDatabaseHas('clients', [
            'nama' => 'Api Client',
            'sumber_client' => 'sosmed',
            'jenis_bisnis' => 'pendidikan',
        ]);
    }

    public function test_list_clients_via_api(): void
    {
        $user = User::factory()->create([
            'role' => 'sales',
        ]);

        Client::factory()->create([
            'nama' => 'Client A',
            'sumber_client' => 'offline',
            'jenis_bisnis' => 'industri',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/clients');

        $response->assertOk();
        $response->assertJsonFragment([
            'nama' => 'Client A',
        ]);
    }

    public function test_store_custom_business_type_via_api(): void
    {
        $user = User::factory()->create([
            'role' => 'sales',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/clients', [
            'nama' => 'Api Custom Biz',
            'perusahaan' => 'PT Custom API',
            'nomor_wa' => '081399998888',
            'sumber_client' => 'website',
            'jenis_bisnis' => 'ketik_sendiri',
            'jenis_bisnis_custom' => 'retail modern',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.jenis_bisnis', 'retail modern');

        $this->assertDatabaseHas('clients', [
            'nama' => 'Api Custom Biz',
            'jenis_bisnis' => 'retail modern',
        ]);
    }
}
