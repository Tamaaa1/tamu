<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'dinas_id',
        'name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relasi ke master_dinas
     */
    public function masterDinas()
    {
        return $this->belongsTo(MasterDinas::class, 'dinas_id', 'dinas_id');
    }

    /**
     * Relasi ke agenda sebagai koordinator
     */
    public function agendaKoordinator()
    {
        return $this->hasMany(Agenda::class, 'nama_koordinator', 'username');
    }

    /**
     * Get the user's dinas name
     */
    public function getDinasNameAttribute()
    {
        return $this->masterDinas ? $this->masterDinas->nama_dinas : 'Tidak ada dinas';
    }
}
