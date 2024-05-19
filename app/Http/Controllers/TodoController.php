<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    public function index()
    {
        $todosQuery = auth()->user()->todos()->latest();
        $todos = $this->filter($todosQuery);
        $todos = $todos->get();
        return response()->json([
            'status' => 'success',
            'todos' => $todos,
        ]);
    }

    private function filter($query)
    {
        return $query
            ->when(request('id'), function ($q) {
                $q->where('id', request('id'));
            })
            ->when(request('title'),  function ($q) {
                $q->where('title', 'LIKE', '%' . request('title') . '%');
            });
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $todo = Todo::create([
            'title' => $request->title,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Todo created successfully',
            'todo' => $todo,
        ]);
    }

    public function show($id)
    {
        $todo = Todo::where([['id', $id], ['user_id', auth()->user()->id]])->get();
        if (count($todo) === 0) {
            return response()->json([
                'status' => 'error',
                'message' => '404 not found'
            ], 404);
        }
        else {
            return response()->json([
                'status' => 'success',
                'todo' => $todo,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string|max:255',
            'deadline_date' => 'nullable|date_format:Y-m-d',
            'deadline_time' => 'nullable|date_format:H:i:s'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $todo = Todo::find($id);
        $todo->description = $request->description;
        $todo->deadline_date = $request->deadline_date;
        $todo->deadline_time = $request->deadline_time;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully',
            'todo' => $todo,
        ]);
    }

    public function markAsDone(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:todos,id',
        ]);

        $todo = Todo::find($request->id);
        $todo->done = 1;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo marked as done successfully',
            'todo' => $todo,
        ]);
    }

    public function markAsUndone(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:todos,id',
        ]);

        $todo = Todo::find($request->id);
        $todo->done = 0;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo marked as undone successfully',
            'todo' => $todo,
        ]);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
}
