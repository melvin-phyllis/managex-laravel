<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Payroll;
use App\Models\User;
use Database\Seeders\PayrollCIVSeeder;
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

        $this->seed(PayrollCIVSeeder::class);

        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->create();

        // Create an active contract for the employee (required by PayrollService)
        Contract::create([
            'user_id' => $this->employee->id,
            'contract_type' => 'stage',
            'base_salary' => 500000,
            'start_date' => now()->subMonths(6)->toDateString(),
            'is_current' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_payrolls_list(): void
    {
        Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->month,
            'annee' => now()->year,
            'gross_salary' => 500000,
            'net_salary' => 420000,
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
            'mois' => now()->month,
            'annee' => now()->year,
            'bonuses' => 50000,
            'overtime_amount' => 0,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payrolls', [
            'user_id' => $this->employee->id,
            'mois' => now()->month,
            'annee' => now()->year,
        ]);
    }

    /** @test */
    public function admin_can_update_payroll(): void
    {
        $payroll = Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->month,
            'annee' => now()->year,
            'gross_salary' => 500000,
            'net_salary' => 420000,
            'bonuses' => 0,
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.payrolls.update', $payroll), [
            'bonuses' => 75000,
            'notes' => 'Updated payroll',
        ]);

        $response->assertRedirect();
        $payroll->refresh();
        $this->assertEquals(75000, $payroll->bonuses);
    }

    /** @test */
    public function admin_can_mark_payroll_as_paid(): void
    {
        $payroll = Payroll::create([
            'user_id' => $this->employee->id,
            'mois' => now()->month,
            'annee' => now()->year,
            'gross_salary' => 500000,
            'net_salary' => 420000,
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payrolls.mark-paid', $payroll));

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
            'mois' => now()->month,
            'annee' => now()->year,
            'gross_salary' => 500000,
            'net_salary' => 420000,
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
            'mois' => now()->month,
            'annee' => now()->year,
            'gross_salary' => 500000,
            'net_salary' => 420000,
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
            'mois' => now()->month,
            'annee' => now()->year,
            'gross_salary' => 500000,
            'net_salary' => 420000,
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
            'mois' => now()->month,
            'annee' => now()->year,
            'gross_salary' => 500000,
            'net_salary' => 420000,
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payrolls.store'), [
            'user_id' => $this->employee->id,
            'mois' => now()->month,
            'annee' => now()->year,
        ]);

        // Should have validation error or redirect with error
        $this->assertEquals(1, Payroll::where('user_id', $this->employee->id)
            ->where('mois', now()->month)
            ->where('annee', now()->year)
            ->count());
    }

    /** @test */
    public function payroll_calculates_net_salary(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.payrolls.store'), [
            'user_id' => $this->employee->id,
            'mois' => now()->month,
            'annee' => now()->year,
            'bonuses' => 0,
            'overtime_amount' => 0,
        ]);

        $payroll = Payroll::where('user_id', $this->employee->id)
            ->where('mois', now()->month)
            ->where('annee', now()->year)
            ->first();

        // Net should be less than or equal to gross (after deductions)
        $this->assertNotNull($payroll);
        $this->assertLessThanOrEqual($payroll->gross_salary, $payroll->net_salary);
    }
}
