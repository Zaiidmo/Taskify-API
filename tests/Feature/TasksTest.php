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
    public function updateMyTask()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create a task owned by the authenticated user
        $task = Task::factory()->create(['user_id' => $user->id]);

        // New data for updating the task
        $newData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ];

        // Invoke the update method
        $response = $this->putJson("/api/tasks/{$task->id}", $newData);

        // Assert the response
        $response->assertStatus(200)->assertJson([
            'message' => 'Task updated successfully',
            'task' => $newData,
        ]);

        // Refresh the task model instance from the database
        $task->refresh();

        // Assert that the task was updated with the new data
        $this->assertEquals($newData['title'], $task->title);
        $this->assertEquals($newData['description'], $task->description);
    }

    /** @test */
    public function unauthorizedUpdater()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create a task owned by a different user
        $task = Task::factory()->create();

        // New data for updating the task
        $newData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ];

        // Invoke the update method
        $response = $this->putJson("/api/tasks/{$task->id}", $newData);

        // Assert the response
        $response->assertStatus(401)->assertJson(['message' => 'You are not authorized to update this task']);

        // Ensure that the task was not updated
        $task->refresh();
        $this->assertNotEquals($newData['title'], $task->title);
        $this->assertNotEquals($newData['description'], $task->description);
    }
    /** @test */
    public function deleteMyTask()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create a task owned by the authenticated user
        $task = Task::factory()->create(['user_id' => $user->id]);

        // Invoke the destroy method
        $response = $this->deleteJson("/api/tasks/{$task->id}");

        // Assert the response
        $response->assertStatus(200)->assertJson(['message' => 'Task deleted successfully']);

        // Ensure that the task was deleted from the database
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function unauthorizedDelete()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create a task owned by a different user
        $task = Task::factory()->create();

        // Invoke the destroy method
        $response = $this->deleteJson("/api/tasks/{$task->id}");

        // Assert the response
        $response->assertStatus(401)->assertJson(['message' => 'You are not authorized to delete this task']);

        // Ensure that the task was not deleted from the database
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
