<!-- Sidebar -->
<div id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-40 transform -translate-x-full md:translate-x-0 transition-transform ease-in-out duration-300" aria-hidden="true">
    <!-- Logo/Brand -->
    <div class="p-6 border-b border-gray-200">
        <!-- Close button for mobile -->
        <button onclick="closeSidebar()" aria-label="Close sidebar" class="md:hidden absolute right-4 top-4 p-2 rounded-md text-gray-600 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Chronoboros</h1>
        <p class="text-xs text-gray-500 mt-1">Task Management</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-4">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a 
                    href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors group"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Tasks -->
            <li>
                <a 
                    href="{{ route('tasks.index') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('tasks.index') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} transition-colors group"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span class="font-medium">Tasks</span>
                </a>
            </li>

            <!-- Reminders -->
            <li>
                <a 
                    href="#" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors group"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="font-medium">Reminders</span>
                </a>
            </li>

            <!-- Archive -->
            <li>
                <a 
                    href="{{ route('tasks.archived') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('tasks.archived') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} transition-colors group"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <span class="font-medium">Archive</span>
                </a>
            </li>

            <!-- Divider -->
            <li class="pt-4 pb-2">
                <hr class="border-gray-200">
            </li>

            <!-- Add New Task (Quick Action) -->
            <li>
                <button 
                    onclick="openModal()"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="font-medium">Add New Task</span>
                </button>
            </li>
        </ul>
    </nav>

    <!-- Bottom Section (Optional - User Info, Settings, etc.) -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                U
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-800">User Name</p>
                <p class="text-xs text-gray-500">user@email.com</p>
            </div>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
    </div>
</div>