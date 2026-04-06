<?php

namespace Tests\Feature\Api;

use App\Models\Lead;
use App\Models\Quotation;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CrmWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_authentication(): void
    {
        $response = $this->getJson('/api/leads');

        $response->assertUnauthorized();
    }

    public function test_store_lead_creates_initial_status_history_with_actor(): void
    {
        $actor = User::factory()->create();
        $assignee = User::factory()->create();
        Sanctum::actingAs($actor);

        $response = $this->postJson('/api/leads', [
            'nama_client' => 'PT Alpha',
            'perusahaan' => 'Alpha Corp',
            'no_hp' => '08123456789',
            'email' => 'alpha@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'Cold',
            'assigned_to' => $assignee->id,
            'notes' => 'Lead awal',
        ]);

        $response->assertCreated();

        $lead = Lead::firstOrFail();
        $history = $lead->statusHistories()->firstOrFail();

        $this->assertSame('Cold', $history->to_status);
        $this->assertNull($history->from_status);
        $this->assertSame($actor->id, $history->changed_by);
    }

    public function test_update_lead_status_creates_status_history_entry(): void
    {
        $actor = User::factory()->create();

        $lead = Lead::create([
            'nama_client' => 'Beta',
            'perusahaan' => 'Beta Ltd',
            'no_hp' => '0899999999',
            'email' => 'beta@example.test',
            'alamat' => 'Bandung',
            'sumber_lead' => 'ig',
            'status' => 'Cold',
            'assigned_to' => $actor->id,
            'notes' => 'Initial',
        ]);

        Sanctum::actingAs($actor);

        $response = $this->putJson("/api/leads/{$lead->id}", [
            'nama_client' => 'Beta',
            'perusahaan' => 'Beta Ltd',
            'no_hp' => '0899999999',
            'email' => 'beta@example.test',
            'alamat' => 'Bandung',
            'sumber_lead' => 'ig',
            'status' => 'Hot',
            'assigned_to' => $actor->id,
            'notes' => 'Escalated',
        ]);

        $response->assertOk();

        $history = $lead->fresh()->statusHistories()->firstOrFail();
        $this->assertSame('Cold', $history->from_status);
        $this->assertSame('Hot', $history->to_status);
        $this->assertSame($actor->id, $history->changed_by);
    }

    public function test_non_owner_cannot_access_other_user_lead(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $lead = Lead::create([
            'nama_client' => 'Private Lead',
            'perusahaan' => 'PT Private',
            'no_hp' => '0891111222',
            'email' => 'private@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'Warm',
            'assigned_to' => $owner->id,
            'notes' => null,
        ]);

        Sanctum::actingAs($otherUser);

        $this->getJson("/api/leads/{$lead->id}")->assertForbidden();
        $this->putJson("/api/leads/{$lead->id}", [
            'nama_client' => 'Private Lead Updated',
            'perusahaan' => 'PT Private',
            'no_hp' => '0891111222',
            'email' => 'private@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'Hot',
            'assigned_to' => $owner->id,
            'notes' => null,
        ])->assertForbidden();
        $this->deleteJson("/api/leads/{$lead->id}")->assertForbidden();
    }

    public function test_store_quotation_rejects_non_hot_lead(): void
    {
        $actor = User::factory()->create();
        Sanctum::actingAs($actor);

        $lead = Lead::create([
            'nama_client' => 'Gamma',
            'perusahaan' => 'Gamma Inc',
            'no_hp' => '0877777777',
            'email' => 'gamma@example.test',
            'alamat' => 'Surabaya',
            'sumber_lead' => 'referensi',
            'status' => 'Warm',
            'assigned_to' => $actor->id,
            'notes' => 'Warm lead',
        ]);

        $response = $this->postJson('/api/quotations', [
            'lead_id' => $lead->id,
            'tanggal_penawaran' => now()->toDateString(),
            'nomor_penawaran' => 'Q-001',
            'nilai_penawaran' => 1500000,
            'status' => 'pending',
            'keterangan' => 'Penawaran awal',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Quotation hanya dapat dibuat untuk lead dengan status Hot.',
            ]);

        $this->assertDatabaseCount('quotations', 0);
    }

    public function test_updating_quotation_to_accepted_sets_lead_to_deal_and_creates_history(): void
    {
        $actor = User::factory()->create();
        Sanctum::actingAs($actor);

        $lead = Lead::create([
            'nama_client' => 'Delta',
            'perusahaan' => 'Delta Group',
            'no_hp' => '0866666666',
            'email' => 'delta@example.test',
            'alamat' => 'Yogyakarta',
            'sumber_lead' => 'website',
            'status' => 'Hot',
            'assigned_to' => $actor->id,
            'notes' => 'Hot lead',
        ]);

        $quotation = Quotation::create([
            'lead_id' => $lead->id,
            'tanggal_penawaran' => now()->toDateString(),
            'nomor_penawaran' => 'Q-002',
            'nilai_penawaran' => 2500000,
            'status' => 'pending',
            'keterangan' => 'Penawaran final',
        ]);

        $response = $this->putJson("/api/quotations/{$quotation->id}", [
            'tanggal_penawaran' => now()->toDateString(),
            'nomor_penawaran' => 'Q-002',
            'nilai_penawaran' => 2500000,
            'status' => 'accepted',
            'keterangan' => 'Disetujui',
        ]);

        $response->assertOk();

        $lead = $lead->fresh();

        $this->assertSame('Deal', $lead->status);
        $this->assertDatabaseHas('lead_status_histories', [
            'lead_id' => $lead->id,
            'from_status' => 'Hot',
            'to_status' => 'Deal',
            'changed_by' => $actor->id,
        ]);
    }

    public function test_report_summary_endpoint_returns_overview_payload(): void
    {
        $actor = User::factory()->create();
        Sanctum::actingAs($actor);

        Lead::create([
            'nama_client' => 'Report Lead',
            'perusahaan' => 'PT Report',
            'no_hp' => '0812121212',
            'email' => 'reportlead@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'Deal',
            'assigned_to' => $actor->id,
            'notes' => null,
        ]);

        $response = $this->getJson('/api/reports/summary?year='.now()->year);

        $response->assertOk();
        $response->assertJsonStructure([
            'filters' => ['year', 'sales_id', 'status', 'sumber_lead', 'from_date', 'to_date'],
            'overview' => ['total_leads', 'deal_total', 'lost_total', 'pipeline_value'],
            'monthly_closing',
        ]);
    }

    public function test_report_sales_monthly_endpoint_returns_grouped_data(): void
    {
        $actor = User::factory()->create();
        Sanctum::actingAs($actor);

        $lead = Lead::create([
            'nama_client' => 'Monthly Lead',
            'perusahaan' => 'PT Monthly',
            'no_hp' => '0812999999',
            'email' => 'monthlylead@example.test',
            'alamat' => 'Bandung',
            'sumber_lead' => 'referensi',
            'status' => 'Hot',
            'assigned_to' => $actor->id,
            'notes' => null,
        ]);

        Quotation::create([
            'lead_id' => $lead->id,
            'tanggal_penawaran' => now()->toDateString(),
            'nomor_penawaran' => 'Q-MONTHLY-01',
            'nilai_penawaran' => 3000000,
            'status' => 'pending',
            'keterangan' => null,
        ]);

        $response = $this->getJson('/api/reports/sales-monthly?year='.now()->year);

        $response->assertOk();
        $response->assertJsonStructure([
            'filters' => ['year', 'sales_id', 'status', 'sumber_lead', 'from_date', 'to_date'],
            'data' => [
                '*' => ['month', 'sales', 'total_leads', 'deal_total', 'lost_total', 'pipeline_value'],
            ],
        ]);
    }

    public function test_report_funnel_conversion_endpoint_returns_transition_metrics(): void
    {
        $actor = User::factory()->create();
        Sanctum::actingAs($actor);

        $lead = Lead::create([
            'nama_client' => 'Funnel Lead',
            'perusahaan' => 'PT Funnel',
            'no_hp' => '0812555555',
            'email' => 'funnel@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'Hot',
            'assigned_to' => $actor->id,
            'notes' => null,
        ]);

        $lead->statusHistories()->create([
            'from_status' => 'Cold',
            'to_status' => 'Warm',
            'changed_by' => $actor->id,
            'changed_at' => now()->subDays(2),
            'note' => 'Cold ke Warm',
        ]);

        $lead->statusHistories()->create([
            'from_status' => 'Warm',
            'to_status' => 'Hot',
            'changed_by' => $actor->id,
            'changed_at' => now()->subDay(),
            'note' => 'Warm ke Hot',
        ]);

        $response = $this->getJson('/api/reports/funnel-conversion?year='.now()->year);

        $response->assertOk();
        $response->assertJsonStructure([
            'filters' => ['year', 'sales_id', 'status', 'sumber_lead', 'from_date', 'to_date'],
            'transitions' => [
                '*' => ['label', 'success', 'drop', 'rate'],
            ],
        ]);
        $response->assertJsonFragment([
            'label' => 'Cold ke Warm',
            'success' => 1,
        ]);
    }

    public function test_report_followups_health_endpoint_returns_overdue_grouping(): void
    {
        $sales = User::factory()->create([
            'name' => 'Sales Health',
            'email' => 'health@example.test',
        ]);

        Sanctum::actingAs($sales);

        $lead = Lead::create([
            'nama_client' => 'Lead Health',
            'perusahaan' => 'PT Health',
            'no_hp' => '0812666666',
            'email' => 'lead-health@example.test',
            'alamat' => 'Jakarta',
            'sumber_lead' => 'website',
            'status' => 'Warm',
            'assigned_to' => $sales->id,
            'notes' => null,
        ]);

        Activity::create([
            'lead_id' => $lead->id,
            'tanggal' => now()->subDays(2)->toDateString(),
            'jenis' => 'call',
            'catatan' => 'Reminder overdue',
            'next_follow_up' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/reports/followups-health');

        $response->assertOk();
        $response->assertJsonStructure([
            'target_date',
            'total_overdue',
            'unassigned_overdue',
            'sales' => [
                '*' => [
                    'sales_id',
                    'sales_name',
                    'sales_email',
                    'overdue_count',
                    'next_oldest_followup',
                    'next_latest_followup',
                    'sample_leads',
                ],
            ],
        ]);

        $response->assertJsonFragment([
            'sales_name' => 'Sales Health',
            'overdue_count' => 1,
        ]);
    }
}
