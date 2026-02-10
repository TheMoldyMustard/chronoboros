<!-- Add Task Modal -->
<div id="addTaskModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop with blur -->
    <div class="fixed inset-0 bg-black/50" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto relative z-10">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b border-gray-200 z-10 rounded-t-lg">
                <h3 class="text-2xl font-semibold text-gray-900">Add New Task</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Subject Dropdown -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">
                                Subject
                            </label>
                            <button 
                                type="button"
                                onclick="openAddSubjectModal()"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                            >
                                + Add New Subject
                            </button>
                        </div>
                        <select 
                            id="subject_id" 
                            name="subject_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                        >
                            <option value="">General</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->subject_id }}">{{ $subject->subject_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Due Within This Day Checkbox -->
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="due_today" 
                                name="due_today" 
                                value="1"
                                checked
                                onchange="toggleDueToday()"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="text-sm font-medium text-gray-700">
                                Due within the day (sets time to 11:59 PM)
                            </span>
                        </label>
                    </div>

                    <!-- Task Title -->
                    <div class="md:col-span-2">
                        <label for="task_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Task Title <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="task_title" 
                            name="task_title" 
                            maxlength="45"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                            placeholder="Enter task title"
                        >
                    </div>

                    <!-- Task Description -->
                    <div class="md:col-span-2">
                        <label for="task_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea 
                            id="task_description" 
                            name="task_description" 
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all resize-none"
                            placeholder="Enter task description (optional)"
                        ></textarea>
                    </div>

                    <!-- Deadline Date -->
                    <div>
                        <label for="deadline_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline Date
                        </label>
                        <input 
                            type="date" 
                            id="deadline_date" 
                            name="deadline_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                        >
                    </div>

                    <!-- Deadline Time -->
                    <div>
                        <label for="deadline_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline Time
                        </label>
                        <input 
                            type="time" 
                            id="deadline_time" 
                            name="deadline_time"
                            value="23:59"
                            disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                    </div>
                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority
                        </label>
                        <input 
                            type="number" 
                            id="priority" 
                            name="priority"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                            placeholder="Enter priority level"
                        >
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            Color Tag
                        </label>
                        <input 
                            type="color" 
                            id="color" 
                            name="color"
                            value="#3B82F6"
                            class="w-full h-10 px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all cursor-pointer"
                        >
                    </div>

                    <!-- File Upload -->
                    <div class="md:col-span-2">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            Attach File (Optional)
                        </label>
                        <input 
                            type="file" 
                            id="file" 
                            name="file"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                    </div>

                    <!-- File Description -->
                    <div class="md:col-span-2">
                        <label for="file_desc" class="block text-sm font-medium text-gray-700 mb-2">
                            File Description (Optional)
                        </label>
                        <textarea 
                            id="file_desc" 
                            name="file_desc" 
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all resize-none"
                            placeholder="Describe the attached file"
                        ></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button 
                        type="button" 
                        onclick="closeModal()"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        Add Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Subject Modal (keep as is) -->
<div id="addSubjectModal" class="fixed inset-0 z-[60] overflow-y-auto hidden">
    <!-- Backdrop with blur -->
    <div class="fixed inset-0 bg-black/60" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 relative z-10">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Add New Subject</h3>
                <button type="button" onclick="closeAddSubjectModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('subjects.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Subject Name -->
                <div class="mb-4">
                    <label for="subject_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="subject_name" 
                        name="subject_name" 
                        maxlength="100"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                        placeholder="Enter subject name"
                    >
                </div>

                <!-- Subject Color -->
                <div class="mb-6">
                    <label for="subject_color" class="block text-sm font-medium text-gray-700 mb-2">
                        Color <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="color" 
                        id="subject_color" 
                        name="color"
                        value="#3B82F6"
                        required
                        class="w-full h-10 px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all cursor-pointer"
                    >
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="closeAddSubjectModal()"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        Add Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleDueToday() {
        const checkbox = document.getElementById('due_today');
        const timeInput = document.getElementById('deadline_time');
        
        if (checkbox.checked) {
            timeInput.value = '23:59';
            timeInput.disabled = true;
        } else {
            timeInput.disabled = false;
            timeInput.value = '';
        }
    }

    function toggleEditDueToday() {
        const checkbox = document.getElementById('edit_due_today');
        const timeInput = document.getElementById('edit_deadline_time');
        
        if (checkbox.checked) {
            timeInput.value = '23:59';
            timeInput.disabled = true;
        } else {
            timeInput.disabled = false;
        }
    }

    function openModal() {
        const modal = document.getElementById('addTaskModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        toggleDueToday();
    }

    function closeModal() {
        const modal = document.getElementById('addTaskModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.querySelector('#addTaskModal form').reset();
        document.getElementById('due_today').checked = true;
        toggleDueToday();
    }

    

    function closeEditModal() {
        document.getElementById('editTaskModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('editTaskForm').reset();
    }

    function openAddSubjectModal() {
        document.getElementById('addSubjectModal').classList.remove('hidden');
    }

    function closeAddSubjectModal() {
        document.getElementById('addSubjectModal').classList.add('hidden');
        document.querySelector('#addSubjectModal form').reset();
    }

    // Close modals when clicking on backdrop
    document.getElementById('addTaskModal')?.addEventListener('click', function(e) {
        if (e.target === this || e.target === this.firstElementChild) {
            closeModal();
        }
    });

    document.getElementById('editTaskModal')?.addEventListener('click', function(e) {
        if (e.target === this || e.target === this.firstElementChild) {
            closeEditModal();
        }
    });

    document.getElementById('addSubjectModal')?.addEventListener('click', function(e) {
        if (e.target === this || e.target === this.firstElementChild) {
            closeAddSubjectModal();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeEditModal();
            closeAddSubjectModal();
        }
    });
</script>