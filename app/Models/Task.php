<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Specify the table name (if different from 'tasks')
    protected $table = 'tasks';
    
    // Specify the primary key column name
    protected $primaryKey = 'task_id';
    
    // Tell Laravel NOT to manage created_at/updated_at timestamps
    // (since your table uses 'create_date' instead)
    public $timestamps = false;
    
    // Allow mass assignment for these fields
    protected $fillable = [
        'task_title',
        'task_description',
        'deadline_date',
        'deadline_time',
        'create_date',
        'priority',
        'color'
    ];
    
    // Cast attributes to specific types
    protected $casts = [
        'deadline_date' => 'date',
        'create_date' => 'datetime',
        'priority' => 'integer',
    ];
}