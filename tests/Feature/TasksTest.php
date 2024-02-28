<?php

namespace Tests\Feature\API;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function displayMyTasks()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create tasks for the user
        $tasks = Task::factory()
            ->count(3)
            ->create(['user_id' => $user->id]);

        // Invoke the index method
        $response = $this->getJson('/api/tasks');

        // Assert the response
        $response->assertOk()->assertJson($tasks->toArray());
    }
    /** @test */
    public function createATask()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Define task data
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
        ];

        // Invoke the store method
        $response = $this->postJson('/api/tasks', $taskData);

        // Assert the response
        $response->assertStatus(200)->assertJson([
            'message' => 'Task created successfully',
            'task' => $taskData,
        ]);

        // Assert that the task was created in the database
        $this->assertDatabaseHas('tasks', $taskData);
    }
    /** @test */
    
}
