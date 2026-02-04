<?php

namespace Tests\Feature;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->employee = User::factory()->create();
    }

    /** @test */
    public function employee_can_view_their_leaves(): void
    {
        Leave::create([
            'user_id' => $this->employee->id,
            'type' => 'conge',
            'date_debut' => now()->addDays(5),
            'date_fin' => now()->addDays(10),
            'motif' => 'Vacances',
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->employee)->get(route('employee.leaves.index'));

        $response->assertStatus(200);
        $response->assertSee('Vacances');
    }

    /** @test */
    public function employee_can_create_leave_request(): void
    {
        $response = $this->actingAs($this->employee)->post(route('employee.leaves.store'), [
            'type' => 'conge',
            'date_debut' => now()->addDays(10)->format('Y-m-d'),
            'date_fin' => now()->addDays(15)->format('Y-m-d'),
            'motif' => 'Vacances familiales',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leaves', [
            'user_id' => $this->employee->id,
            'type' => 'conge',
            'motif' => 'Vacances familiales',
            'statut' => 'pending',
        ]);
    }

    /** @test */
    public function admin_can_view_all_leave_requests(): void
    {
        Leave::create([
            'user_id' => $this->employee->id,
            'type' => 'conge',
            'date_debut' => now()->addDays(5),
            'date_fin' => now()->addDays(10),
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.leaves.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_approve_leave_request(): void
    {
        $leave = Leave::create([
            'user_id' => $this->employee->id,
            'type' => 'conge',
            'date_debut' => now()->addDays(5),
            'date_fin' => now()->addDays(10),
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.leaves.approve', $leave), [
            'commentaire_admin' => 'Approuvé, bonnes vacances !',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'statut' => 'approved',
        ]);
    }

    /** @test */
    public function admin_can_reject_leave_request(): void
    {
        $leave = Leave::create([
            'user_id' => $this->employee->id,
            'type' => 'conge',
            'date_debut' => now()->addDays(5),
            'date_fin' => now()->addDays(10),
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.leaves.reject', $leave), [
            'commentaire_admin' => 'Période trop chargée',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'statut' => 'rejected',
            'commentaire_admin' => 'Période trop chargée',
        ]);
    }

    /** @test */
    public function employee_cannot_cancel_approved_leave(): void
    {
        $leave = Leave::create([
            'user_id' => $this->employee->id,
            'type' => 'conge',
            'date_debut' => now()->addDays(5),
            'date_fin' => now()->addDays(10),
            'statut' => 'approved',
        ]);

        $response = $this->actingAs($this->employee)->delete(route('employee.leaves.destroy', $leave));

        // Should fail or redirect with error
        $this->assertDatabaseHas('leaves', ['id' => $leave->id]);
    }

    /** @test */
    public function employee_can_cancel_pending_leave(): void
    {
        $leave = Leave::create([
            'user_id' => $this->employee->id,
            'type' => 'conge',
            'date_debut' => now()->addDays(5),
            'date_fin' => now()->addDays(10),
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->employee)->delete(route('employee.leaves.destroy', $leave));

        $response->assertRedirect();
        $this->assertDatabaseMissing('leaves', ['id' => $leave->id]);
    }

    /** @test */
    public function leave_request_requires_valid_dates(): void
    {
        $response = $this->actingAs($this->employee)->post(route('employee.leaves.store'), [
            'type' => 'conge',
            'date_debut' => now()->addDays(15)->format('Y-m-d'),
            'date_fin' => now()->addDays(10)->format('Y-m-d'), // End before start
            'motif' => 'Test',
        ]);

        $response->assertSessionHasErrors(['date_fin']);
    }

    /** @test */
    public function leave_request_cannot_be_in_the_past(): void
    {
        $response = $this->actingAs($this->employee)->post(route('employee.leaves.store'), [
            'type' => 'conge',
            'date_debut' => now()->subDays(5)->format('Y-m-d'),
            'date_fin' => now()->subDays(1)->format('Y-m-d'),
            'motif' => 'Test',
        ]);

        $response->assertSessionHasErrors(['date_debut']);
    }

    /** @test */
    public function employee_cannot_view_other_employee_leave(): void
    {
        $otherEmployee = User::factory()->create();
        $leave = Leave::create([
            'user_id' => $otherEmployee->id,
            'type' => 'conge',
            'date_debut' => now()->addDays(5),
            'date_fin' => now()->addDays(10),
            'statut' => 'pending',
        ]);

        $response = $this->actingAs($this->employee)->get(route('employee.leaves.show', $leave));

        $response->assertStatus(403);
    }
}
