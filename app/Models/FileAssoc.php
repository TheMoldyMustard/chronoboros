<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAssoc extends Model
{
    protected $table = 'files_assoc';
    protected $primaryKey = 'assoc_id';
    public $timestamps = false;
    
    protected $fillable = [
        'file_name',
        'task_id',
        'file_desc'
    ];

    // Relationship with Task
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }
}