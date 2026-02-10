<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\FileAssoc;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'deadline');
        $sortOrder = $request->get('sort_order', 'asc');

        // Only get active (non-archived) tasks
        $query = Task::with(['files', 'subject'])->active();

        switch ($sortBy) {
            case 'deadline':
                $query->orderByRaw('
                    CASE 
                        WHEN deadline_date IS NULL THEN 1 
                        ELSE 0 
                    END, 
                    deadline_date ASC, 
                    deadline_time ASC,
                    CASE 
                        WHEN priority IS NULL THEN 0 
                        ELSE priority 
                    END DESC
                ');
                break;
            
            case 'priority':
                $query->orderByRaw('
                    CASE 
                        WHEN priority IS NULL OR priority = 0 THEN 0 
                        ELSE priority 
                    END DESC,
                    CASE 
                        WHEN deadline_date IS NULL THEN 1 
                        ELSE 0 
                    END, 
                    deadline_date ASC
                ');
                break;
            
            case 'subject':
                $query->leftJoin('subjects', 'tasks.subject_id', '=', 'subjects.subject_id')
                    ->select('tasks.*')
                    ->orderByRaw('
                        CASE 
                            WHEN subjects.subject_name IS NULL THEN "ZZZZ" 
                            ELSE subjects.subject_name 
                        END ASC,
                        CASE 
                            WHEN deadline_date IS NULL THEN 1 
                            ELSE 0 
                        END, 
                        deadline_date ASC
                    ');
                break;
            
            case 'created':
                $query->orderBy('create_date', $sortOrder);
                break;
            
            default:
                $query->orderByRaw('
                    CASE 
                        WHEN deadline_date IS NULL THEN 1 
                        ELSE 0 
                    END, 
                    deadline_date ASC, 
                    deadline_time ASC,
                    CASE 
                        WHEN priority IS NULL THEN 0 
                        ELSE priority 
                    END DESC
                ');
        }

        $tasks = $query->get();
        $subjects = Subject::all();
        
        return view('home', compact('tasks', 'subjects', 'sortBy', 'sortOrder'));
    }

    public function archived(Request $request)
    {
        $sortBy = $request->get('sort_by', 'archived');
        
        // Get only archived tasks
        $query = Task::with(['files', 'subject'])->archived();

        if ($sortBy === 'archived') {
            $query->orderBy('archived_on', 'desc');
        } else {
            $query->orderBy('create_date', 'desc');
        }

        $tasks = $query->get();
        $subjects = Subject::all();
        
        return view('archived', compact('tasks', 'subjects', 'sortBy'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_title' => 'required|max:45',
            'task_description' => 'nullable',
            'deadline_date' => 'nullable|date',
            'deadline_time' => 'nullable',
            'due_today' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'color' => 'nullable|size:7',
            'subject_id' => 'nullable|exists:subjects,subject_id',
            'file' => 'nullable|file|max:10240',
            'file_desc' => 'nullable|string',
        ]);

        if ($request->has('due_today') && $request->due_today) {
            if ($request->deadline_date) {
                $validated['deadline_time'] = '23:59:00';
            } else {
                $validated['deadline_time'] = null;
            }
        }

        $validated['create_date'] = now();
        $validated['is_archived'] = 0;

        unset($validated['due_today']);

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

    public function edit($id)
    {
        try {
            $task = Task::with(['files', 'subject'])->findOrFail($id);
            $subjects = Subject::all();
            
            return response()->json([
                'task' => $task,
                'subjects' => $subjects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'task_title' => 'required|max:45',
            'task_description' => 'nullable',
            'deadline_date' => 'nullable|date',
            'deadline_time' => 'nullable',
            'due_today' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'color' => 'nullable|size:7',
            'subject_id' => 'nullable|exists:subjects,subject_id',
            'file' => 'nullable|file|max:10240',
            'file_desc' => 'nullable|string',
        ]);

        if ($request->has('due_today') && $request->due_today) {
            if ($request->deadline_date) {
                $validated['deadline_time'] = '23:59:00';
            } else {
                $validated['deadline_time'] = null;
            }
        }

        unset($validated['due_today']);

        $task->update($validated);

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

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function archive($id)
    {
        $task = Task::findOrFail($id);
        
        $task->update([
            'is_archived' => 1,
            'archived_on' => now()
        ]);

        return redirect()->back()->with('success', 'Task archived successfully!');
    }

    public function unarchive($id)
    {
        $task = Task::findOrFail($id);
        
        $task->update([
            'is_archived' => 0,
            'archived_on' => null
        ]);

        return redirect()->back()->with('success', 'Task restored successfully!');
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        foreach ($task->files as $file) {
            $filePath = public_path('uploads/' . $file->file_name);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $file->delete();
        }
        
        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
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