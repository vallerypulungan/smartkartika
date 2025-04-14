<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Child extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_child';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['name', 'id_class', 'id_parent'];

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'id_parent');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'id_class');
    }
}
