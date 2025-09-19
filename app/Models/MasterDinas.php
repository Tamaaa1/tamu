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

    // Boot method untuk cache invalidation
    protected static function boot()
    {
        parent::boot();

        // Invalidate cache saat master dinas diupdate, create, atau delete
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('master_dinas');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('master_dinas');
        });
    }
}
