<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agenda_details';

    protected $fillable = [
        'agenda_id',
        'nama',
        'dinas_id',
        'jabatan',
        'gender',
        'no_hp',
        'gambar_ttd',
    ];
    // relasi ke agenda
    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id', 'id');
    }
    // relasi ke master_dinas
    public function masterDinas()
    {
        return $this->belongsTo(MasterDinas::class, 'dinas_id', 'dinas_id');
    }


}
