<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - Chronoboros</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .task-expanded {
            max-height: 1000px;
            transition: max-height 0.3s ease-in-out;
        }
        .task-collapsed {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Include Sidebar -->
    @include('components.sidebar')

    <!-- Main Content Area (with left margin for sidebar) -->
    <div class="ml-64 py-8 px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header with Add Button -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-4xl font-bold text-gray-800">Tasks</h1>
                
                <div class="flex items-center gap-3">
                    <select onchange="window.location.href='?sort_by='+this.value" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="deadline" {{ $sortBy == 'deadline' ? 'selected' : '' }}>Sort by Deadline</option>
                        <option value="priority" {{ $sortBy == 'priority' ? 'selected' : '' }}>Sort by Priority</option>
                        <option value="subject" {{ $sortBy == 'subject' ? 'selected' : '' }}>Sort by Subject</option>
                        <option value="created" {{ $sortBy == 'created' ? 'selected' : '' }}>Sort by Created</option>
                    </select>
                    
                    <button 
                        onclick="openModal()"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-md hover:shadow-lg flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Task
                    </button>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            <!-- Tasks List -->
            <div class="space-y-3">
                @forelse($tasks as $task)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden">
                        <!-- Colored Left Border -->
                        <div class="flex">
                            <div class="w-1.5 flex-shrink-0" style="background-color: {{ $task->color ?? '#3B82F6' }};"></div>
                            
                            <div class="flex-1">
                                <!-- Collapsed View (Always Visible) -->
                                <div 
                                    onclick="toggleTask({{ $task->task_id }})" 
                                    class="p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                                >
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-4 flex-1 min-w-0">
                                            <!-- Subject Badge -->
                                            @if($task->subject)
                                                <span class="inline-block px-3 py-1 rounded-md text-xs font-semibold flex-shrink-0" 
                                                      style="background-color: {{ $task->subject->color }}; color: {{ $task->subject->getTextColor() }};">
                                                    {{ $task->subject->subject_name }}
                                                </span>
                                            @else
                                                <span class="inline-block px-3 py-1 rounded-md text-xs font-semibold bg-gray-200 text-gray-700 flex-shrink-0">
                                                    General
                                                </span>
                                            @endif

                                            <!-- Title -->
                                            <h2 class="text-lg font-semibold text-gray-900 truncate flex-1">
                                                {{ $task->task_title }}
                                            </h2>

                                            <!-- Deadline -->
                                            @if($task->deadline_date || $task->deadline_time)
                                                <div class="flex items-center gap-2 text-sm text-gray-500 flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="hidden sm:inline">
                                                        @if($task->deadline_date)
                                                            {{ \Carbon\Carbon::parse($task->deadline_date)->format('M d, Y') }}
                                                        @endif
                                                        @if($task->deadline_time)
                                                            {{ \Carbon\Carbon::parse($task->deadline_time)->format('g:i A') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif

                                            <!-- Priority Badge -->
                                            @if($task->priority)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap flex-shrink-0">
                                                    P{{ $task->priority }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Expand Icon -->
                                        <svg 
                                            id="icon-{{ $task->task_id }}" 
                                            class="w-5 h-5 text-gray-400 transition-transform duration-300 flex-shrink-0" 
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Expanded View (Hidden by default) -->
                                <div id="task-{{ $task->task_id }}" class="task-collapsed">
                                    <div class="px-4 pb-4 pt-2 border-t border-gray-100">
                                        <!-- Description -->
                                        @if($task->task_description)
                                            <div class="mb-4">
                                                <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                                                <p class="text-gray-600 leading-relaxed">{{ $task->task_description }}</p>
                                            </div>
                                        @endif

                                        <!-- Files Section -->
                                        @if($task->files && $task->files->count() > 0)
                                            <div class="mb-4">
                                                <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    Attached Files ({{ $task->files->count() }})
                                                </h3>
                                                <div class="space-y-2">
                                                    @foreach($task->files as $file)
                                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                            <div class="flex-1">
                                                                <a 
                                                                    href="{{ asset('uploads/' . $file->file_name) }}" 
                                                                    target="_blank"
                                                                    class="text-blue-600 hover:text-blue-700 font-medium text-sm"
                                                                >
                                                                    {{ $file->file_name }}
                                                                </a>
                                                                @if($file->file_desc)
                                                                    <p class="text-xs text-gray-500 mt-1">{{ $file->file_desc }}</p>
                                                                @endif
                                                            </div>
                                                            <a 
                                                                href="{{ asset('uploads/' . $file->file_name) }}" 
                                                                download
                                                                class="ml-4 p-2 text-gray-400 hover:text-gray-600"
                                                            >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        <div class="flex gap-2">
                                            <button 
                                                onclick="event.stopPropagation(); editTask({{ $task->task_id }})"
                                                class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button 
                                                onclick="event.stopPropagation(); deleteTask({{ $task->task_id }})"
                                                class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                            <button 
                                                onclick="event.stopPropagation(); archiveTask({{ $task->task_id }})"
                                                class="flex-1 px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                </svg>
                                                Archive
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new task.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Include Modal -->
    @include('components.add-task-modal', ['subjects' => $subjects])

    <script>
        function toggleTask(taskId) {
            const taskElement = document.getElementById('task-' + taskId);
            const iconElement = document.getElementById('icon-' + taskId);
            
            if (taskElement.classList.contains('task-collapsed')) {
                taskElement.classList.remove('task-collapsed');
                taskElement.classList.add('task-expanded');
                iconElement.style.transform = 'rotate(180deg)';
            } else {
                taskElement.classList.add('task-collapsed');
                taskElement.classList.remove('task-expanded');
                iconElement.style.transform = 'rotate(0deg)';
            }
        }

        function editTask(taskId) {
            // TODO: Implement edit functionality
            console.log('Edit task:', taskId);
            alert('Edit functionality coming soon!');
        }

        function deleteTask(taskId) {
            // TODO: Implement delete functionality
            if (confirm('Are you sure you want to delete this task?')) {
                console.log('Delete task:', taskId);
                alert('Delete functionality coming soon!');
            }
        }

        function archiveTask(taskId) {
            // TODO: Implement archive functionality
            console.log('Archive task:', taskId);
            alert('Archive functionality coming soon!');
        }
    </script>
</body>
</html>