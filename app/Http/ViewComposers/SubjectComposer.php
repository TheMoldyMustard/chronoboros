<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Subject;

class SubjectComposer
{
    public function compose(View $view)
    {
        $view->with('subjects', Subject::all());
    }
}