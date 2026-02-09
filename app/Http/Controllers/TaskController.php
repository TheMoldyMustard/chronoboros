<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\FileAssoc;
use App\Models\Subject;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['files', 'subject'])
            ->orderByRaw('priority IS NULL, priority DESC')
            ->get();
        $subjects = Subject::all();
        return view('home', compact('tasks', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_title' => 'required|max:45',
            'task_description' => 'required',
            'deadline_date' => 'nullable|date',
            'deadline_time' => 'nullable',
            'priority' => 'nullable|integer',
            'color' => 'nullable|size:7',
            'subject_id' => 'nullable|exists:subjects,subject_id',
            'file' => 'nullable|file|max:10240',
            'file_desc' => 'nullable|string',
        ]);

        $validated['create_date'] = now();

        $task = Task::create($validated);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);

            FileAssoc::create([
                'file_name' => $fileName,
                'task_id' => $task->task_id,
                'file_desc' => $request->file_desc
            ]);
        }

        return redirect()->back()->with('success', 'Task added successfully!');
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => 'required|max:100|unique:subjects,subject_name',
            'color' => 'required|size:7',
        ]);

        Subject::create($validated);

        return redirect()->back()->with('success', 'Subject added successfully!');
    }
}