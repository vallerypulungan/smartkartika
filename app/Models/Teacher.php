<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_teacher';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['name', 'email', 'password', 'num_telp', 'nip'];

    public function activities()
    {
        return $this->hasMany(Activity::class, 'id_teacher');
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class, 'id_teacher');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'id_teacher');
    }
}
