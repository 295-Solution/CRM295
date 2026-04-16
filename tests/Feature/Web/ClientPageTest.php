<?php

namespace Tests\Feature\Web;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        Client::factory()->create([
            'nama' => 'Budi Santoso',
            'perusahaan' => 'PT Mitra Abadi',
            'nomor_wa' => '08123456789',
            'sumber_client' => 'relasi',
            'jenis_bisnis' => 'industri',
        ]);

        $response = $this->actingAs($user)->get('/clients');

        $response->assertOk();
        $response->assertSee('Client Workspace');
        $response->assertSee('Budi Santoso');
    }

    public function test_store_client_creates_new_client(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/clients', [
            'nama' => 'Sari Wijaya',
            'perusahaan' => 'CV Maju Terus',
            'nomor_wa' => '081355577799',
            'sumber_client' => 'website',
            'jenis_bisnis' => 'fnb',
        ]);

        $response->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'nama' => 'Sari Wijaya',
            'perusahaan' => 'CV Maju Terus',
            'nomor_wa' => '081355577799',
            'sumber_client' => 'website',
            'jenis_bisnis' => 'fnb',
        ]);
    }

    public function test_store_client_with_custom_business_type_persists_custom_value(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/clients', [
            'nama' => 'Custom Biz',
            'perusahaan' => 'PT Custom',
            'nomor_wa' => '081300000001',
            'sumber_client' => 'offline',
            'jenis_bisnis' => 'ketik_sendiri',
            'jenis_bisnis_custom' => 'kesehatan',
        ]);

        $response->assertRedirect('/clients');

        $this->assertDatabaseHas('clients', [
            'nama' => 'Custom Biz',
            'jenis_bisnis' => 'kesehatan',
        ]);
    }

    public function test_client_detail_page_shows_related_quotations(): void
    {
        $user = User::factory()->create();

        $client = Client::factory()->create([
            'nama' => 'Client Detail',
        ]);

        $lead = Lead::create([
            'nama_client' => 'Legacy Lead',
            'perusahaan' => 'PT Legacy',
            'no_hp' => '08120001111',
            'email' => 'legacy@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'chat_masuk',
            'assigned_to' => $user->id,
            'notes' => null,
        ]);

        Quotation::create([
            'lead_id' => $lead->id,
            'client_id' => $client->id,
            'tanggal_penawaran' => now()->toDateString(),
            'nomor_penawaran' => 'Q-CLIENT-001',
            'nilai_penawaran' => 5000000,
            'hpp' => 3000000,
            'status' => 'berjalan',
            'keterangan' => 'Quotation client detail',
        ]);

        $response = $this->actingAs($user)->get(route('clients.show', $client));

        $response->assertOk();
        $response->assertSee('Client Detail');
        $response->assertSee('Quotation Client');
        $response->assertSee('Q-CLIENT-001');
    }
}
