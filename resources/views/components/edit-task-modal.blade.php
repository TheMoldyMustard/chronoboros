<!-- Edit Task Modal -->
<div id="editTaskModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop with blur -->
    <div class="fixed inset-0 bg-black/50" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto relative z-10">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white flex items-center justify-between p-6 border-b border-gray-200 z-10 rounded-t-lg">
                <h3 class="text-2xl font-semibold text-gray-900">Edit Task</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editTaskForm" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Subject Dropdown -->
                    <div class="md:col-span-2">
                        <label for="edit_subject_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject
                        </label>
                        <select 
                            id="edit_subject_id" 
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
                                id="edit_due_today" 
                                name="due_today" 
                                value="1"
                                onchange="toggleEditDueToday()"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="text-sm font-medium text-gray-700">
                                Due within the day (sets time to 11:59 PM)
                            </span>
                        </label>
                    </div>

                    <!-- Task Title -->
                    <div class="md:col-span-2">
                        <label for="edit_task_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Task Title <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="edit_task_title" 
                            name="task_title" 
                            maxlength="45"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                            placeholder="Enter task title"
                        >
                    </div>

                    <!-- Task Description -->
                    <div class="md:col-span-2">
                        <label for="edit_task_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea 
                            id="edit_task_description" 
                            name="task_description" 
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all resize-none"
                            placeholder="Enter task description (optional)"
                        ></textarea>
                    </div>

                    <!-- Deadline Date -->
                    <div>
                        <label for="edit_deadline_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline Date
                        </label>
                        <input 
                            type="date" 
                            id="edit_deadline_date" 
                            name="deadline_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                        >
                    </div>

                    <!-- Deadline Time -->
                    <div>
                        <label for="edit_deadline_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline Time
                        </label>
                        <input 
                            type="time" 
                            id="edit_deadline_time" 
                            name="deadline_time"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="edit_priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority
                        </label>
                        <input 
                            type="number" 
                            id="edit_priority" 
                            name="priority"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                            placeholder="Enter priority level"
                        >
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="edit_color" class="block text-sm font-medium text-gray-700 mb-2">
                            Color Tag
                        </label>
                        <input 
                            type="color" 
                            id="edit_color" 
                            name="color"
                            class="w-full h-10 px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all cursor-pointer"
                        >
                    </div>

                    <!-- File Upload -->
                    <div class="md:col-span-2">
                        <label for="edit_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Attach Additional File (Optional)
                        </label>
                        <input 
                            type="file" 
                            id="edit_file" 
                            name="file"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                    </div>

                    <!-- File Description -->
                    <div class="md:col-span-2">
                        <label for="edit_file_desc" class="block text-sm font-medium text-gray-700 mb-2">
                            File Description (Optional)
                        </label>
                        <textarea 
                            id="edit_file_desc" 
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
                        onclick="closeEditModal()"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        Update Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>