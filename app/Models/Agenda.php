<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * Model untuk tabel agendas
 *
 * Mewakili data agenda/rapat/acara yang dapat didaftarkan pesertanya.
 * Mengelola relasi dengan master dinas, koordinator, dan detail peserta.
 *
 * @property int $id Primary key
 * @property int $dinas_id Foreign key ke master_dinas
 * @property string $nama_agenda Nama agenda/acara
 * @property \Carbon\Carbon $tanggal_agenda Tanggal pelaksanaan agenda
 * @property string $nama_koordinator Nama koordinator (diambil dari user login)
 * @property string|null $link_acara Link untuk pendaftaran publik
 * @property bool $link_active Status aktif/nonaktif agenda
 * @property string|null $unique_token Token unik untuk QR code
 * @property \Carbon\Carbon $created_at Timestamp created
 * @property \Carbon\Carbon $updated_at Timestamp updated
 *
 * @property-read \App\Models\MasterDinas $masterDinas Relasi ke master dinas
 * @property-read \App\Models\User $koordinator Relasi ke user koordinator
 * @property-read \Illuminate\Database\Eloquent\Collection $agendaDetail Collection detail peserta
 */
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
        'unique_token',
    ];

    protected $casts = [
        'tanggal_agenda' => 'date',
        'link_active' => 'boolean',
    ];

    /**
     * Relasi ke master dinas
     *
     * Agenda belongs to MasterDinas (many-to-one relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function masterDinas()
    {
        return $this->belongsTo(MasterDinas::class, 'dinas_id', 'dinas_id');
    }

    /**
     * Relasi ke detail peserta agenda
     *
     * Agenda has many AgendaDetail (one-to-many relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agendaDetail()
    {
        return $this->hasMany(AgendaDetail::class, 'agenda_id', 'id');
    }

    /**
     * Relasi ke user sebagai koordinator
     *
     * Agenda belongs to User melalui field nama_koordinator (many-to-one relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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

    /**
     * Accessor untuk format tanggal yang readable
     *
     * Mengembalikan tanggal agenda dalam format "d F Y" (contoh: 15 Januari 2025)
     *
     * @return string Tanggal agenda dalam format readable
     */
    public function getTanggalAgendaFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_agenda)->format('d F Y');
    }

    /**
     * Accessor untuk status agenda berdasarkan tanggal dan aktif/tidak
     *
     * Menentukan status agenda: Tidak Aktif, Selesai, Sedang Berlangsung, atau Akan Datang
     *
     * @return string Status agenda
     */
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

    /**
     * Accessor untuk mendapatkan jumlah peserta terdaftar
     *
     * Menghitung jumlah peserta yang terdaftar untuk agenda ini
     *
     * @return int Jumlah peserta terdaftar
     */
    public function getJumlahPesertaAttribute()
    {
        return $this->agendaDetail()->count();
    }

    /**
     * Generate unique token untuk agenda
     *
     * Membuat token unik 16 karakter hex untuk QR code agenda.
     * Memastikan token tidak duplikat dengan agenda lain.
     *
     * @return string Token unik untuk agenda
     */
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

    // Boot method untuk auto-generate token saat create dan cache invalidation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agenda) {
            if (empty($agenda->unique_token)) {
                $agenda->unique_token = self::generateUniqueToken();
            }
        });

        // Invalidate cache saat agenda diupdate, create, atau delete
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('agendas_all');
            \Illuminate\Support\Facades\Cache::forget('active_agendas_' . now()->format('Y-m-d'));
            \Illuminate\Support\Facades\Cache::forget('active_agendas_' . now()->format('Y-m-d') . '_list');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('agendas_all');
            \Illuminate\Support\Facades\Cache::forget('active_agendas_' . now()->format('Y-m-d'));
            \Illuminate\Support\Facades\Cache::forget('active_agendas_' . now()->format('Y-m-d') . '_list');
        });
    }
}
