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
    public function it_returns_tasks_for_authenticated_user()
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
    
}
