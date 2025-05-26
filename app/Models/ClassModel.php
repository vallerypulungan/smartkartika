<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'id_class';
    protected $fillable = ['class', 'class_level'];

    public function children()
    {
        return $this->hasMany(Child::class, 'id_class');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'id_class');
    }
}
