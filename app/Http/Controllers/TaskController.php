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
        // Get sorting parameters from request (for future sorting feature)
        $sortBy = $request->get('sort_by', 'deadline'); // default: deadline
        $sortOrder = $request->get('sort_order', 'asc'); // default: ascending

        $query = Task::with(['files', 'subject']);

        // Apply sorting based on parameters
        switch ($sortBy) {
            case 'deadline':
                // Sort by deadline (nulls last), then by priority (desc)
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
                // Sort by priority (nulls/0 last), then by deadline
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
                // Sort by subject name, then by deadline
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
                // Sort by creation date
                $query->orderBy('create_date', $sortOrder);
                break;
            
            default:
                // Default: deadline sorting
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

        // Handle "Due within the day" checkbox
        if ($request->has('due_today') && $request->due_today) {
            // Only set time to 23:59 if a date is actually provided
            if ($request->deadline_date) {
                $validated['deadline_time'] = '23:59:00';
            } else {
                // If checkbox is checked but no date, don't set time either
                $validated['deadline_time'] = null;
            }
        }

        $validated['create_date'] = now();

        // Remove due_today from validated data (not a database column)
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

        // Handle "Due within the day" checkbox
        if ($request->has('due_today') && $request->due_today) {
            if ($request->deadline_date) {
                $validated['deadline_time'] = '23:59:00';
            } else {
                $validated['deadline_time'] = null;
            }
        }

        // Remove due_today from validated data
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

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        // Save title before deletion
        $title = $task->task_title;

        foreach ($task->files as $file) {
            $filePath = public_path('uploads/' . $file->file_name);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $file->delete();
        }

        $task->delete();

        return redirect()->back()->with('success', "Task \"{$title}\" deleted successfully!");
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