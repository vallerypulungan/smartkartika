<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_teacher', 'id_class', 'id_parent', 'id_child', 'file'
    ];

    public function teacher() {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }
    public function class() {
        return $this->belongsTo(ClassModel::class, 'id_class');
    }
    public function parent() {
        return $this->belongsTo(ParentModel::class, 'id_parent');
    }
    public function child() {
        return $this->belongsTo(Child::class, 'id_child');
    }
}
