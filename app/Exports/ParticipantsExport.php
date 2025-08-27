<?php

namespace App\Exports;

use App\Models\AgendaDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ParticipantsExport implements FromCollection, WithHeadings, WithMapping, WithDrawings
{
    protected $participants;

    public function __construct($participants)
    {
        $this->participants = $participants;
    }

    public function collection()
    {
        return $this->participants;
    }

    public function map($participant): array
    {
        return [
            $participant->id,
            $participant->nama,
            $participant->jabatan,
            $participant->masterDinas->nama_dinas ?? 'N/A',
            $participant->agenda->nama_agenda ?? 'N/A',
            $participant->created_at->format('d/m/Y'),
            $participant->gambar_ttd ? '': 'Tidak Ada'
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Jabatan',
            'Dinas',
            'Agenda',
            'Tanggal Daftar',
            'Tanda Tangan',
        ];
    }

    public function drawings()
    {
        $drawings = [];
        $row = 2; 

        foreach ($this->participants as $participant) {
            if ($participant->gambar_ttd && file_exists(storage_path('app/public/' . $participant->gambar_ttd))) {
                $drawing = new Drawing();
                $drawing->setName('Tanda Tangan ' . $participant->nama);
                $drawing->setDescription('Tanda Tangan ' . $participant->nama);
                $drawing->setPath(storage_path('app/public/' . $participant->gambar_ttd));
                $drawing->setHeight(40);
                $drawing->setWidth(80);
                $drawing->setCoordinates('G' . $row);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                
                $drawings[] = $drawing;
            }
            $row++;
        }

        return $drawings;
    }
}
