<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'task_id';
    public $timestamps = false;
    
    protected $fillable = [
        'task_title',
        'task_description',
        'deadline_date',
        'deadline_time',
        'create_date',
        'priority',
        'color',
        'subject_id',
        'is_archived',
        'archived_on'
    ];
    
    protected $casts = [
        'deadline_date' => 'date',
        'create_date' => 'datetime',
        'archived_on' => 'datetime',
        'priority' => 'integer',
        'is_archived' => 'boolean',
    ];

    // Relationship with Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    // Relationship with FileAssoc
    public function files()
    {
        return $this->hasMany(FileAssoc::class, 'task_id', 'task_id');
    }

    // Scope to get only active (non-archived) tasks
    public function scopeActive($query)
    {
        return $query->where('is_archived', 0);
    }

    // Scope to get only archived tasks
    public function scopeArchived($query)
    {
        return $query->where('is_archived', 1);
    }
}