<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['report_content', 'id_activity', 'id_teacher', 'id_document'];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'id_activity');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }

    public function documentation()
    {
        return $this->belongsTo(Documentation::class, 'id_document');
    }
}
