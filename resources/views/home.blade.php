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
        
        /* Ensure touch targets are at least 44x44px on mobile */
        @media (max-width: 768px) {
            button, a {
                min-height: 44px;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Include Sidebar -->
    @include('components.sidebar')

    <!-- Sidebar overlay (mobile) -->
    <div id="sidebar-overlay" onclick="closeSidebar()" class="fixed inset-0 bg-black/50 z-30 hidden" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);" aria-hidden="true"></div>
    
    <!-- Toast Container -->
    <div id="toast-container"
        class="fixed bottom-4 right-4 left-4 sm:left-auto sm:bottom-6 sm:right-6 z-50 flex flex-col gap-3 items-stretch sm:items-end">
        
        @if(session('success'))
            <div class="toast bg-green-600 text-white px-4 py-3 sm:px-6 sm:py-4 rounded-xl shadow-xl
                        transition-transform duration-300 translate-y-8">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium text-sm sm:text-base">
                        {{ session('success') }}
                    </span>
                </div>
            </div>
        @endif

    </div>

    <!-- Main Content Area (with left margin for sidebar on md+) -->
    <div class="py-4 px-3 sm:py-6 sm:px-4 md:py-8 md:px-6 md:ml-64">
        <!-- Header with Add Button -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6 sm:mb-8">
            <!-- Title and Mobile Menu -->
            <div class="flex items-center justify-between w-full sm:w-auto">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">Tasks</h1>
                
                <!-- Mobile hamburger -->
                <button onclick="openSidebar()" aria-label="Open sidebar" 
                    class="md:hidden p-2 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 active:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Controls Row -->
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <!-- Sort Dropdown -->
                <select onchange="window.location.href='?sort_by='+this.value" 
                    class="flex-1 sm:flex-none px-3 py-2 sm:px-4 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white">
                    <option value="deadline" {{ $sortBy == 'deadline' ? 'selected' : '' }}>
                        <span class="hidden sm:inline">Sort by </span>Deadline
                    </option>
                    <option value="priority" {{ $sortBy == 'priority' ? 'selected' : '' }}>
                        <span class="hidden sm:inline">Sort by </span>Priority
                    </option>
                    <option value="subject" {{ $sortBy == 'subject' ? 'selected' : '' }}>
                        <span class="hidden sm:inline">Sort by </span>Subject
                    </option>
                    <option value="created" {{ $sortBy == 'created' ? 'selected' : '' }}>
                        <span class="hidden sm:inline">Sort by </span>Created
                    </option>
                </select>
                
                <!-- Add Task Button -->
                <button 
                    onclick="openModal()"
                    class="flex-1 sm:flex-none px-4 py-2 sm:px-6 sm:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-colors font-medium shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-sm sm:text-base whitespace-nowrap"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden xs:inline">Add Task</span>
                    <span class="xs:hidden">Add</span>
                </button>
            </div>
        </div>
  
        <!-- Tasks List -->
        <div class="space-y-2 sm:space-y-3">
            @forelse($tasks as $task)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200 overflow-hidden">
                    <!-- Colored Left Border -->
                    <div class="flex">
                        <div class="w-1 sm:w-1.5 flex-shrink-0" style="background-color: {{ $task->color ?? '#3B82F6' }};"></div>
                        
                        <div class="flex-1 min-w-0">
                            <!-- Collapsed View (Always Visible) -->
                            <div 
                                onclick="toggleTask({{ $task->task_id }})" 
                                class="p-3 sm:p-4 cursor-pointer hover:bg-gray-50 active:bg-gray-100 transition-colors"
                            >
                                <!-- Mobile Layout (< 640px) -->
                                <div class="sm:hidden space-y-2">
                                    <!-- First Row: Subject and Priority -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <!-- Subject Badge -->
                                            @if($task->subject)
                                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold flex-shrink-0" 
                                                      style="background-color: {{ $task->subject->color }}; color: {{ $task->subject->getTextColor() }};">
                                                    {{ $task->subject->subject_name }}
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-gray-200 text-gray-700 flex-shrink-0">
                                                    General
                                                </span>
                                            @endif

                                            <!-- Priority Badge -->
                                            @if($task->priority)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap flex-shrink-0">
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

                                    <!-- Second Row: Title -->
                                    <h2 class="text-base font-semibold text-gray-900 line-clamp-2">
                                        {{ $task->task_title }}
                                    </h2>

                                    <!-- Third Row: Deadline -->
                                    @if($task->deadline_date || $task->deadline_time)
                                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                </div>

                                <!-- Tablet/Desktop Layout (>= 640px) -->
                                <div class="hidden sm:flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 md:gap-4 flex-1 min-w-0">
                                        <!-- Subject Badge -->
                                        @if($task->subject)
                                            <span class="inline-block px-2.5 py-1 md:px-3 rounded-md text-xs font-semibold flex-shrink-0" 
                                                  style="background-color: {{ $task->subject->color }}; color: {{ $task->subject->getTextColor() }};">
                                                {{ $task->subject->subject_name }}
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 md:px-3 rounded-md text-xs font-semibold bg-gray-200 text-gray-700 flex-shrink-0">
                                                General
                                            </span>
                                        @endif

                                        <!-- Title -->
                                        <h2 class="text-base md:text-lg font-semibold text-gray-900 truncate flex-1">
                                            {{ $task->task_title }}
                                        </h2>

                                        <!-- Deadline -->
                                        @if($task->deadline_date || $task->deadline_time)
                                            <div class="flex items-center gap-2 text-sm text-gray-500 flex-shrink-0">
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

                                        <!-- Priority Badge -->
                                        @if($task->priority)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap flex-shrink-0">
                                                P{{ $task->priority }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Expand Icon -->
                                    <svg 
                                        id="icon-tablet-{{ $task->task_id }}" 
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
                                <div class="px-3 pb-3 pt-2 sm:px-4 sm:pb-4 border-t border-gray-100">
                                    <!-- Description -->
                                    @if($task->task_description)
                                        <div class="mb-3 sm:mb-4">
                                            <h3 class="text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Description</h3>
                                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ $task->task_description }}</p>
                                        </div>
                                    @endif

                                    <!-- Files Section -->
                                    @if($task->files && $task->files->count() > 0)
                                        <div class="mb-3 sm:mb-4">
                                            <h3 class="text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2 flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                Attached Files ({{ $task->files->count() }})
                                            </h3>
                                            <div class="space-y-2">
                                                @foreach($task->files as $file)
                                                    <div class="flex items-center justify-between p-2.5 sm:p-3 bg-gray-50 rounded-lg gap-2">
                                                        <div class="flex-1 min-w-0">
                                                            <a 
                                                                href="{{ asset('uploads/' . $file->file_name) }}" 
                                                                target="_blank"
                                                                class="text-blue-600 hover:text-blue-700 font-medium text-xs sm:text-sm break-all"
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
                                                            class="flex-shrink-0 p-2 text-gray-400 hover:text-gray-600 active:text-gray-700"
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
                                    <!-- Mobile: Stacked Buttons -->
                                    <div class="flex flex-col sm:hidden gap-2">
                                        <button 
                                            onclick="event.stopPropagation(); editTask({{ $task->task_id }})"
                                            class="w-full px-4 py-2.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 active:bg-blue-200 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit Task
                                        </button>
                                        <div class="flex gap-2">
                                            <button 
                                                onclick="event.stopPropagation(); deleteTask({{ $task->task_id }})"
                                                class="flex-1 px-4 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 active:bg-red-200 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                            <button 
                                                onclick="event.stopPropagation(); archiveTask({{ $task->task_id }})"
                                                class="flex-1 px-4 py-2.5 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                </svg>
                                                Archive
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Tablet/Desktop: Horizontal Buttons -->
                                    <div class="hidden sm:flex gap-2">
                                        <button 
                                            onclick="event.stopPropagation(); editTask({{ $task->task_id }})"
                                            class="flex-1 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 active:bg-blue-200 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button 
                                            onclick="event.stopPropagation(); deleteTask({{ $task->task_id }})"
                                            class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 active:bg-red-200 transition-colors font-medium text-sm flex items-center justify-center gap-2"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                        <button 
                                            onclick="event.stopPropagation(); archiveTask({{ $task->task_id }})"
                                            class="flex-1 px-4 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-colors font-medium text-sm flex items-center justify-center gap-2"
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
                <div class="text-center py-8 sm:py-12 bg-white rounded-lg">
                    <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Get started by creating a new task.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Include Modal -->
    @include('components.add-task-modal', ['subjects' => $subjects])
    @include('components.edit-task-modal', ['subjects' => $subjects])

    <script>
    function toggleTask(taskId) {
        const taskElement = document.getElementById('task-' + taskId);
        const iconElement = document.getElementById('icon-' + taskId);
        const iconTablet = document.getElementById('icon-tablet-' + taskId);
        
        if (taskElement.classList.contains('task-collapsed')) {
            taskElement.classList.remove('task-collapsed');
            taskElement.classList.add('task-expanded');
            if (iconElement) iconElement.style.transform = 'rotate(180deg)';
            if (iconTablet) iconTablet.style.transform = 'rotate(180deg)';
        } else {
            taskElement.classList.add('task-collapsed');
            taskElement.classList.remove('task-expanded');
            if (iconElement) iconElement.style.transform = 'rotate(0deg)';
            if (iconTablet) iconTablet.style.transform = 'rotate(0deg)';
        }
    }

    function editTask(taskId) {
        openEditModal(taskId);
    }

    async function openEditModal(taskId) {
        try {
            const response = await fetch(`/tasks/${taskId}/edit`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            const task = data.task;
            
            // Populate form fields
            document.getElementById('edit_task_title').value = task.task_title || '';
            document.getElementById('edit_task_description').value = task.task_description || '';
            document.getElementById('edit_deadline_date').value = task.deadline_date || '';
            document.getElementById('edit_deadline_time').value = task.deadline_time ? task.deadline_time.substring(0, 5) : '';
            document.getElementById('edit_priority').value = task.priority || '';
            document.getElementById('edit_color').value = task.color || '#3B82F6';
            document.getElementById('edit_subject_id').value = task.subject_id || '';
            
            // Check if time is 23:59 to determine checkbox state
            const isDueToday = task.deadline_time === '23:59:00';
            document.getElementById('edit_due_today').checked = isDueToday;
            toggleEditDueToday();
            
            // Update form action
            const form = document.getElementById('editTaskForm');
            form.action = `/tasks/${taskId}`;
            
            // Add the PUT method field if it doesn't exist
            let methodField = form.querySelector('input[name="_method"]');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);
            } else {
                methodField.value = 'PUT';
            }
            
            // Show modal
            document.getElementById('editTaskModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } catch (error) {
            console.error('Error loading task:', error);
            alert('Failed to load task data. Error: ' + error.message);
        }
    }

    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/tasks/${taskId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function archiveTask(taskId) {
        // TODO: Implement archive functionality
        console.log('Archive task:', taskId);
        alert('Archive functionality coming soon!');
    }

    // Toast notifications
    document.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('#toast-container .toast');

        toasts.forEach((toast, index) => {
            // Slight delay so CSS transition triggers
            setTimeout(() => {
                toast.classList.remove('translate-y-8');
                toast.classList.add('translate-y-0');
            }, 100 + (index * 80));

            // Auto hide
            setTimeout(() => {
                toast.classList.remove('translate-y-0');
                toast.classList.add('translate-y-24');

                setTimeout(() => toast.remove(), 300);
            }, 2000 + (index * 200));
        });
    });

    function archiveTask(taskId) {
        if (confirm('Are you sure you want to archive this task?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/tasks/${taskId}/archive`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>

    <script>
    // Sidebar toggle helpers
    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar) return;
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        sidebar.setAttribute('aria-hidden', 'false');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('block');
        }
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar) return;
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        sidebar.setAttribute('aria-hidden', 'true');
        if (overlay) {
            overlay.classList.remove('block');
            overlay.classList.add('hidden');
        }
        document.body.style.overflow = '';
    }

    // Handle responsive behavior
    function handleSidebarState() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar) return;
        
        if (window.innerWidth >= 768) {
            // Desktop: Show sidebar, hide overlay
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            sidebar.setAttribute('aria-hidden', 'false');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('block');
            }
            document.body.style.overflow = '';
        } else {
            // Mobile: Hide sidebar by default
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            sidebar.setAttribute('aria-hidden', 'true');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('block');
            }
            document.body.style.overflow = '';
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', handleSidebarState);

    // Run on window resize with debounce
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleSidebarState, 100);
    });
    </script>
</body>
</html>