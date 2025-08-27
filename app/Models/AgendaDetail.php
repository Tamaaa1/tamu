<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaDetail extends Model
{
    use HasFactory;

    protected $table = 'agenda_details';

    protected $fillable = [
        'agenda_id',
        'nama',
        'dinas_id',
        'jabatan',
        'no_hp',
        'gambar_ttd',
        'qr_code',
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

    // Accessor untuk mendapatkan QR code sebagai SVG
    public function getQrCodeSvgAttribute()
    {
        if (!$this->qr_code) {
            return null;
        }

        try {
            // Pastikan SimpleSoftwareIO\QrCode\Facades\QrCode tersedia
            if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                return \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($this->qr_code);
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Accessor untuk mendapatkan QR code sebagai base64
    public function getQrCodeBase64Attribute()
    {
        $svg = $this->qr_code_svg;
        if ($svg) {
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
        
        return null;
    }
}
