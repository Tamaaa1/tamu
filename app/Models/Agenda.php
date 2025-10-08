<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Agenda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agendas';

    protected $fillable = [
        'dinas_id',
        'nama_agenda',
        'tanggal_agenda',
        'tempat',
        'waktu',
        'nama_koordinator',
        'link_acara',
        'link_active',
        'unique_token',
    ];

    protected $casts = [
        'tanggal_agenda' => 'date',
        'link_active' => 'boolean',
    ];

    // Relasi ke master dinas
    public function masterDinas()
    {
        return $this->belongsTo(MasterDinas::class, 'dinas_id', 'dinas_id');
    }

    // Relasi ke detail peserta agenda
    public function agendaDetail()
    {
        return $this->hasMany(AgendaDetail::class, 'agenda_id', 'id');
    }

    // Relasi ke user sebagai koordinator
    public function koordinator()
    {
        return $this->belongsTo(User::class, 'nama_koordinator', 'username');
    }

    // Scope untuk agenda aktif
    public function scopeActive($query)
    {
        return $query->where('link_active', true);
    }

    // Scope untuk agenda hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_agenda', today());
    }

    // Scope untuk agenda minggu ini
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('tanggal_agenda', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Scope untuk agenda bulan ini
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal_agenda', now()->month)
                    ->whereYear('tanggal_agenda', now()->year);
    }

    // Scope untuk agenda tahun ini
    public function scopeThisYear($query)
    {
        return $query->whereYear('tanggal_agenda', now()->year);
    }

    // Accessor untuk format tanggal yang readable
    public function getTanggalAgendaFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_agenda)->format('d F Y');
    }

    // Accessor untuk status agenda berdasarkan tanggal dan aktif/tidak
    public function getStatusAttribute()
    {
        if (!$this->link_active) {
            return 'Tidak Aktif';
        }

        $today = today();
        $agendaDate = Carbon::parse($this->tanggal_agenda);

        if ($agendaDate->isPast()) {
            return 'Selesai';
        } elseif ($agendaDate->isToday()) {
            return 'Sedang Berlangsung';
        } else {
            return 'Akan Datang';
        }
    }

    // Accessor untuk mendapatkan jumlah peserta terdaftar
    public function getJumlahPesertaAttribute()
    {
        return $this->agendaDetail()->count();
    }

    // Generate unique token untuk agenda
    public static function generateUniqueToken()
    {
        do {
            $token = bin2hex(random_bytes(8)); // 16 karakter hex (lebih pendek)
        } while (self::where('unique_token', $token)->exists());

        return $token;
    }

    // Scope untuk mencari agenda berdasarkan token
    public function scopeByToken($query, $token)
    {
        return $query->where('unique_token', $token);
    }

    // Boot method untuk auto-generate token saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agenda) {
            if (empty($agenda->unique_token)) {
                $agenda->unique_token = self::generateUniqueToken();
            }
        });
    }

    
    
}
