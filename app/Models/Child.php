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

    protected $fillable = [
        'name', 'id_class', 'id_parent', 'tempat_lahir', 'tanggal_lahir', 'nis', 'gender', 'id_tahun_ajaran'
    ];

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'id_parent');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'id_class');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(\App\Models\TahunAjaran::class, 'id_tahun_ajaran');
    }
}
