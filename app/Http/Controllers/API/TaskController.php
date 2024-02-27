<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mytasks = Task::where('user_id', auth('api')->user()->id)->get();
        return response()->json($mytasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth('api')->user()->id,
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        // Update the task with the new data
        $task->title = $request->title;
        $task->description = $request->description;
        $task->save();

        // Optionally, you can return a response with the updated task
        return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if($task->user_id !== auth('api')->user()->id) {
            return response()->json(['message' => 'You are not authorized to delete this task'], 401);
        }
        Task::destroy($task->id);
        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}
