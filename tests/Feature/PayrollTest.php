<?php

namespace Tests\Feature;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->create([
            'salaire_base' => 500000, // 500,000 FCFA
            'situation_familiale' => 'celibataire',
            'nombre_parts' => 1,
        ]);
    }

    /** @test */
    public function admin_can_view_payrolls_list(): void
    {
        Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'salaire_brut' => 500000,
            'salaire_net' => 420000,
            'statut' => 'paid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.payrolls.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_payroll(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.payrolls.store'), [
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'primes' => 50000,
            'heures_supplementaires' => 0,
            'retenues' => 0,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payrolls', [
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
        ]);
    }

    /** @test */
    public function admin_can_update_payroll(): void
    {
        $payroll = Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'salaire_brut' => 500000,
            'salaire_net' => 420000,
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.payrolls.update', $payroll), [
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 550000,
            'primes' => 0,
            'heures_supplementaires' => 0,
            'retenues' => 0,
        ]);

        $response->assertRedirect();
        $payroll->refresh();
        $this->assertEquals(550000, $payroll->salaire_base);
    }

    /** @test */
    public function admin_can_mark_payroll_as_paid(): void
    {
        $payroll = Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'salaire_brut' => 500000,
            'salaire_net' => 420000,
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.payrolls.status', $payroll), [
            'statut' => 'paid',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payrolls', [
            'id' => $payroll->id,
            'statut' => 'paid',
        ]);
    }

    /** @test */
    public function employee_can_view_their_payrolls(): void
    {
        Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'salaire_brut' => 500000,
            'salaire_net' => 420000,
            'statut' => 'paid',
        ]);

        $response = $this->actingAs($this->employee)->get(route('employee.payrolls.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_view_other_employee_payroll(): void
    {
        $otherEmployee = User::factory()->create();
        $payroll = Payroll::create([
            'user_id' => $otherEmployee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'salaire_brut' => 500000,
            'salaire_net' => 420000,
            'statut' => 'paid',
        ]);

        $response = $this->actingAs($this->employee)->get(route('employee.payrolls.show', $payroll));

        $response->assertStatus(403);
    }

    /** @test */
    public function employee_can_download_payroll_pdf(): void
    {
        $payroll = Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'salaire_brut' => 500000,
            'salaire_net' => 420000,
            'statut' => 'paid',
        ]);

        $response = $this->actingAs($this->employee)->get(route('employee.payrolls.download', $payroll));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function admin_cannot_create_duplicate_payroll_for_same_month(): void
    {
        Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'salaire_brut' => 500000,
            'salaire_net' => 420000,
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payrolls.store'), [
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
        ]);

        // Should have validation error or redirect with error
        $this->assertEquals(1, Payroll::where('user_id', $this->employee->id)
            ->where('mois', now()->format('Y-m'))
            ->count());
    }

    /** @test */
    public function payroll_calculates_net_salary(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.payrolls.store'), [
            'user_id' => $this->employee->id,
            'mois' => now()->format('Y-m'),
            'salaire_base' => 500000,
            'primes' => 0,
            'heures_supplementaires' => 0,
            'retenues' => 0,
        ]);

        $payroll = Payroll::where('user_id', $this->employee->id)
            ->where('mois', now()->format('Y-m'))
            ->first();

        // Net should be less than gross (after deductions)
        $this->assertLessThan($payroll->salaire_brut, $payroll->salaire_net);
    }
}
