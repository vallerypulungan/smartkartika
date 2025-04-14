<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_activity';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['activity_tittle', 'description', 'date', 'id_class', 'id_teacher'];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'id_class');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class, 'id_activity');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'id_activity');
    }
}
