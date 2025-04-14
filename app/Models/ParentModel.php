<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';
    protected $primaryKey = 'id_parent';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['name', 'email', 'password', 'num_telp'];

    public function children()
    {
        return $this->hasMany(Child::class, 'id_parent');
    }
}
