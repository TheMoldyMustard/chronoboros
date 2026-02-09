<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'subject_id';
    public $timestamps = false;
    
    protected $fillable = [
        'subject_name',
        'color'
    ];

    // Relationship with Task
    public function tasks()
    {
        return $this->hasMany(Task::class, 'subject_id', 'subject_id');
    }

    // Helper function to determine if text should be white or black
    public function getTextColor()
    {
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }
}