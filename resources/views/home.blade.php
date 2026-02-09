<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Tasks</h1>
        
        <div class="space-y-4">
            @foreach($tasks as $task)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">{{ $task->task_title }}</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $task->task_description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>