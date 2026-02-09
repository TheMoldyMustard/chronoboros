<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('components.add-task-modal', \App\Http\ViewComposers\SubjectComposer::class);
    }

    public function register(): void
    {
        //
    }
}