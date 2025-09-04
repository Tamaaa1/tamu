<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agendas';

    protected $fillable = [
        'dinas_id',
        'nama_agenda',
        'tanggal_agenda',
        'nama_koordinator',
        'link_acara',
        'link_active',
    ];

    // relasi ke master_dinas
    public function masterDinas()
    {
        return $this->belongsTo(MasterDinas::class, 'dinas_id', 'dinas_id');
    }
    
    // relasi ke agenda_detail
    public function agendaDetail()
    {
        return $this->hasMany(AgendaDetail::class, 'agenda_id', 'id');
    }
    
    // relasi ke user sebagai koordinator
    public function koordinator()
    {
        return $this->belongsTo(User::class, 'nama_koordinator', 'username');
    }
}
