<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header with Add Button -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl font-bold text-gray-800">Tasks</h1>
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

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        <!-- Tasks Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tasks as $task)
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-200 overflow-hidden">
                    <!-- Colored Top Border -->
                    <div class="h-1.5" style="background-color: {{ $task->color ?? '#3B82F6' }};"></div>
                    
                    <!-- Card Content -->
                    <div class="p-6">
                        <!-- Subject Badge -->
                        @if($task->subject)
                            <span class="inline-block px-3 py-1 rounded-md text-xs font-semibold mb-3" 
                                  style="background-color: {{ $task->subject->color }}; color: {{ $task->subject->getTextColor() }};">
                                {{ $task->subject->subject_name }}
                            </span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-md text-xs font-semibold mb-3 bg-gray-200 text-gray-700">
                                General
                            </span>
                        @endif

                        <!-- Priority Badge & Title -->
                        <div class="flex items-start justify-between mb-3">
                            <h2 class="text-xl font-semibold text-gray-900 flex-1 pr-2">{{ $task->task_title }}</h2>
                            @if($task->priority)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap">
                                    Priority: {{ $task->priority }}
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 leading-relaxed mb-4 line-clamp-3">{{ $task->task_description }}</p>

                        <!-- Deadline Info -->
                        @if($task->deadline_date || $task->deadline_time)
                            <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>
                                    @if($task->deadline_date)
                                        {{ \Carbon\Carbon::parse($task->deadline_date)->format('M d, Y') }}
                                    @endif
                                    @if($task->deadline_time)
                                        {{ \Carbon\Carbon::parse($task->deadline_time)->format('g:i A') }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- File Indicator -->
                        @if($task->files && $task->files->count() > 0)
                            <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <span>{{ $task->files->count() }} file(s) attached</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex gap-2 pt-4 border-t border-gray-200">
                            <button 
                                onclick="editTask({{ $task->task_id }})"
                                class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                            <button 
                                onclick="archiveTask({{ $task->task_id }})"
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
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new task.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Include Modal -->
    @include('components.add-task-modal')

    <script>
        function editTask(taskId) {
            // TODO: Implement edit functionality
            console.log('Edit task:', taskId);
            alert('Edit functionality coming soon!');
        }

        function archiveTask(taskId) {
            // TODO: Implement archive functionality
            console.log('Archive task:', taskId);
            alert('Archive functionality coming soon!');
        }
    </script>
</body>
</html>