<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

/**
 * Model untuk tabel agenda_details
 *
 * Mewakili data peserta yang terdaftar dalam agenda/rapat/acara.
 * Mengelola informasi pribadi peserta, jabatan, dan tanda tangan digital.
 *
 * @property int $id Primary key
 * @property int $agenda_id Foreign key ke agendas
 * @property string $nama Nama lengkap peserta
 * @property int $dinas_id Foreign key ke master_dinas
 * @property string $jabatan Jabatan peserta
 * @property string|null $gender Jenis kelamin (L/P)
 * @property string $no_hp Nomor handphone
 * @property string|null $gambar_ttd Path file tanda tangan digital
 * @property \Carbon\Carbon|null $deleted_at Timestamp soft delete
 * @property \Carbon\Carbon $created_at Timestamp created
 * @property \Carbon\Carbon $updated_at Timestamp updated
 *
 * @property-read \App\Models\Agenda $agenda Relasi ke agenda
 * @property-read \App\Models\MasterDinas $masterDinas Relasi ke master dinas
 */
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

    protected $casts = [
        'gender' => 'string',
    ];

    // Validation rules for the model
    public static $rules = [
        'agenda_id' => 'required|exists:agendas,id',
        'nama' => 'required|string|min:2|max:100',
        'dinas_id' => 'required|exists:master_dinas,dinas_id',
        'jabatan' => 'required|string|max:100',
        'gender' => 'nullable|in:L,P',
        'no_hp' => 'required|string|regex:/^(\+62|62|0)[8-9][0-9]{7,11}$/|max:15',
        'gambar_ttd' => 'nullable|string'
    ];

    /**
     * Relationship ke agenda
     *
     * AgendaDetail belongs to Agenda (many-to-one relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id', 'id');
    }

    /**
     * Relationship ke master dinas
     *
     * AgendaDetail belongs to MasterDinas (many-to-one relationship)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function masterDinas()
    {
        return $this->belongsTo(MasterDinas::class, 'dinas_id', 'dinas_id');
    }

    /**
     * Accessor untuk nomor HP yang diformat
     *
     * Mengembalikan nomor HP dalam format Indonesia yang readable
     *
     * @return string Nomor HP yang telah diformat
     */
    public function getNoHpFormattedAttribute()
    {
        return $this->formatPhoneNumber($this->no_hp);
    }

    /**
     * Accessor untuk label jenis kelamin
     *
     * Mengembalikan label jenis kelamin dalam bahasa Indonesia
     *
     * @return string Label jenis kelamin (Laki-laki/Perempuan/Tidak Diketahui)
     */
    public function getGenderLabelAttribute()
    {
        return $this->gender === 'L' ? 'Laki-laki' : ($this->gender === 'P' ? 'Perempuan' : 'Tidak Diketahui');
    }

    // Accessor for full name with title
    public function getNamaLengkapAttribute()
    {
        return $this->nama . ($this->jabatan ? ' (' . $this->jabatan . ')' : '');
    }

    // Scope for filtering by gender
    public function scopeByGender($query, $gender)
    {
        if ($gender && in_array($gender, ['L', 'P'])) {
            return $query->where('gender', $gender);
        }
        return $query;
    }

    // Scope for filtering by dinas
    public function scopeByDinas($query, $dinasId)
    {
        if ($dinasId) {
            return $query->where('dinas_id', $dinasId);
        }
        return $query;
    }

    // Scope for searching by name or position
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    // Scope for filtering by agenda date (optimized with date range)
    public function scopeByAgendaDate($query, $tanggal = null, $bulan = null, $tahun = null, $startDate = null, $endDate = null)
    {
        // Prioritize date range filtering (more efficient)
        if ($startDate && $endDate) {
            return $query->whereHas('agenda', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_agenda', [$startDate, $endDate]);
            });
        } elseif ($startDate) {
            return $query->whereHas('agenda', function($q) use ($startDate) {
                $q->where('tanggal_agenda', '>=', $startDate);
            });
        } elseif ($endDate) {
            return $query->whereHas('agenda', function($q) use ($endDate) {
                $q->where('tanggal_agenda', '<=', $endDate);
            });
        }

        // Backward compatibility for individual date components (less optimal but still supported)
        if ($tanggal && $bulan && $tahun) {
            $date = sprintf('%04d-%02d-%02d', $tahun, $bulan, $tanggal);
            return $query->whereHas('agenda', function($q) use ($date) {
                $q->whereDate('tanggal_agenda', $date);
            });
        }

        return $query;
    }

    // Validate model data
    public static function validateData(array $data)
    {
        return Validator::make($data, self::$rules);
    }

    // Format phone number to Indonesian format
    private function formatPhoneNumber($phone)
    {
        if (!$phone) return null;

        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Handle Indonesian phone number formats
        if (str_starts_with($phone, '62')) {
            // Already in +62 format, convert to 0
            $phone = '0' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8') || str_starts_with($phone, '9')) {
            // Mobile number starting with 8 or 9, add 0 prefix
            $phone = '0' . $phone;
        }

        return $phone;
    }
}
