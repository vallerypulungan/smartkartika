<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Documentation extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_document';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['uploaded_by', 'file_url', 'id_activity', 'id_teacher'];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'id_activity');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }

    public function report()
    {
        return $this->hasOne(Report::class, 'id_document');
    }
}
