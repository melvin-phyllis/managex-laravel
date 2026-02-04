<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
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
    public function admin_can_view_tasks_list(): void
    {
        Task::create([
            'user_id' => $this->employee->id,
            'titre' => 'Test Task',
            'description' => 'Test description',
            'statut' => 'pending',
            'priorite' => 'medium',
            'progression' => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Task');
    }

    /** @test */
    public function admin_can_create_task(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tasks.store'), [
            'user_id' => $this->employee->id,
            'titre' => 'New Task',
            'description' => 'Task description',
            'priorite' => 'high',
            'date_debut' => now()->format('Y-m-d'),
            'date_fin' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        // Task is created with 'approved' status in the controller (auto-approved)
        $this->assertDatabaseHas('tasks', [
            'titre' => 'New Task',
            'user_id' => $this->employee->id,
        ]);
    }

    /** @test */
    public function admin_can_approve_task(): void
    {
        $task = Task::create([
            'user_id' => $this->employee->id,
            'titre' => 'Pending Task',
            'statut' => 'pending',
            'priorite' => 'medium',
            'progression' => 0,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.tasks.approve', $task));

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'statut' => 'approved',
        ]);
    }

    /** @test */
    public function admin_can_validate_completed_task(): void
    {
        $task = Task::create([
            'user_id' => $this->employee->id,
            'titre' => 'Completed Task',
            'statut' => 'completed',
            'priorite' => 'medium',
            'progression' => 100,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.tasks.validate', $task));

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'statut' => 'validated',
        ]);
    }

    /** @test */
    public function employee_can_view_their_tasks(): void
    {
        Task::create([
            'user_id' => $this->employee->id,
            'titre' => 'My Task',
            'statut' => 'approved',
            'priorite' => 'medium',
            'progression' => 50,
        ]);

        $response = $this->actingAs($this->employee)->get(route('employee.tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('My Task');
    }

    /** @test */
    public function employee_can_update_task_progress(): void
    {
        $task = Task::create([
            'user_id' => $this->employee->id,
            'titre' => 'In Progress Task',
            'statut' => 'approved',
            'priorite' => 'medium',
            'progression' => 50,
        ]);

        $response = $this->actingAs($this->employee)->patch(route('employee.tasks.progress', $task), [
            'progression' => 75,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'progression' => 75,
        ]);
    }

    /** @test */
    public function task_becomes_completed_at_100_percent(): void
    {
        $task = Task::create([
            'user_id' => $this->employee->id,
            'titre' => 'Almost Done Task',
            'statut' => 'approved',
            'priorite' => 'medium',
            'progression' => 90,
        ]);

        $response = $this->actingAs($this->employee)->patch(route('employee.tasks.progress', $task), [
            'progression' => 100,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'progression' => 100,
            'statut' => 'completed',
        ]);
    }

    /** @test */
    public function employee_cannot_view_other_employee_tasks(): void
    {
        $otherEmployee = User::factory()->create();
        $task = Task::create([
            'user_id' => $otherEmployee->id,
            'titre' => 'Other Task',
            'statut' => 'approved',
            'priorite' => 'medium',
            'progression' => 0,
        ]);

        $response = $this->actingAs($this->employee)->get(route('employee.tasks.show', $task));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_task(): void
    {
        $task = Task::create([
            'user_id' => $this->employee->id,
            'titre' => 'Task to Delete',
            'statut' => 'pending',
            'priorite' => 'low',
            'progression' => 0,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.tasks.destroy', $task));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function task_creation_requires_valid_data(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tasks.store'), [
            'user_id' => null,
            'titre' => '',
            'priorite' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['user_id', 'titre', 'priorite']);
    }
}
