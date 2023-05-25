<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::where('user_id', auth()->user()->id)
            ->orderBy('is_complete', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($todos);
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
            ->where('is_complete', true)
            ->count();
        $categories = Category::where("user_id", auth()->user()->id)->get();

        return view('todo.index', compact('todos', 'todosCompleted', 'categories'));
    }

    public function create()
    {

        $categories = Category::where('user_id', auth()->user()->id)
            ->orderBy('id', 'asc')
            ->get();

        return view('todo.create', compact('categories'));
    }

    public function edit(Todo $todo)
    {
        //  if (auth()->user()->id == $todo->user_id){
        //   return view('todo.edit', compact('todo'));
        //}else{
        //  return redirect()->route('todo.index')->with('danger', 'You are not authorized to edit this todo!');

        //CODE AFTER REFACTORING
        if (auth()->user()->id = $todo->user_id) {
            $categories = Category::where('user_id', auth()->user()->id)
                ->get();
            return view('todo.edit', compact('todo', 'categories'));
        }
        return redirect()->route('todo.index')->with('success', 'Todo updated successfully!');
    }

    public function update(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);
        $todo->update([
            'title' => ucfirst($request->title),
            'category_id' => ucfirst($request->category_id)
        ]);

        return redirect()->route('todo.index')->with('success', 'Todo updated successfully!');
    }

    public function complete(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->update([
                'is_complete' => true,
            ]);
            return redirect()->route('todo.index')->with('success', 'Todo completed successfully!');
        } else {
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to complete this');
        }
    }

    public function uncomplete(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->update([
                'is_complete' => false,
            ]);
            return redirect()->route('todo.index')->with('success', 'Todo uncompleted successfully!');
        } else {
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to complete this');
        }
    }

    public function destroy(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->delete();
            return redirect()->route('todo.index')->with('success', 'Todo deleted successfully!');
        } else {
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to delete this todo!');
        }
    }
    public function destroyCompleted()
    {
        // get all todos for current user where is_completed is true
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
            ->where('is_complete', true)
            ->get();
        foreach ($todosCompleted as $todo) {
            $todo->delete();
        }
        // dd($todosCompleted);
        return redirect()->route('todo.index')->with('success', 'All completed todos deleted successfully!');
    }

    public function store(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'nullable'
        ]);

        // Practical
        // $todo = new Todo;
        // $todo->title = $request->title;
        // $todo->user_id = auth()->user()->id;
        // $todo->save();

        // Query Builder way
        // DB::table('todos)->insert([
        // 'title' => $request->title,
        // 'user_id' => auth()->user()->id,
        // 'created_at' => now(),
        // 'updated_at' => now(),
        // ])

        // Eloquent Way - Readable

        $todo = Todo::create([
            'title' => ucfirst($request->title),
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id
        ]);

        // dd($todo);

        return redirect()->route('todo.index')->with('success', 'Todo created successfully!');
    }
}
