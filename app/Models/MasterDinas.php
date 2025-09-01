<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterDinas extends Model
{
    use HasFactory;

    protected $table = 'master_dinas';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'dinas_id',
        'nama_dinas',
        'email',
        'alamat',
    ];
    
    // relasi ke users
    public function users()
    {
        return $this->hasMany(User::class, 'dinas_id', 'dinas_id');
    }
    
    // relasi ke agenda
    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'dinas_id', 'dinas_id');
    }
}
