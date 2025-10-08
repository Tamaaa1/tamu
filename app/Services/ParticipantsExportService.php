<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\AgendaDetail;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ParticipantsExportService
{
    use Filterable;

    /**
     * Generate PDF export for participants
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        // Optimize memory and execution time
        $this->optimizeMemorySettings();

        // Build and execute query
        $participants = $this->buildParticipantsQuery($request);

        // Get agenda filter info
        $agendaFilter = $this->getAgendaFilter($request);

        // Process logo for PDF
        $logoBase64 = $this->processLogo();

        // Configure and generate PDF
        $pdf = $this->generatePdf($participants, $agendaFilter, $logoBase64);

        // Generate filename
        $filename = $this->generateFilename();

        return $pdf->download($filename);
    }

    /**
     * Optimize PHP settings for large PDF exports
     */
    private function optimizeMemorySettings()
    {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 120);
    }

    /**
     * Build the participants query with filters and ordering
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function buildParticipantsQuery(Request $request)
    {
        $query = AgendaDetail::with(['agenda', 'masterDinas']);

        // Apply filters using trait
        $query = $this->applyDateFilters($query, $request);
        $query = $this->applyAgendaFilter($query, $request);

        // Apply custom ordering by job position priority
        $query = $this->applyPositionOrdering($query);

        // Handle large datasets with chunking
        return $this->handleLargeDataset($query);
    }

    /**
     * Apply custom ordering based on job position hierarchy
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyPositionOrdering($query)
    {
        return $query->orderByRaw("
            CASE
                WHEN LOWER(jabatan) LIKE '%kepala dinas%' THEN 0
                WHEN LOWER(jabatan) LIKE '%sekretaris dinas' OR LOWER(jabatan) LIKE '%sekretaris%' THEN 1
                WHEN LOWER(jabatan) LIKE '%kepala bidang, kabid%' THEN 2
                WHEN LOWER(jabatan) LIKE '%kepala seksi%' OR LOWER(jabatan) LIKE '%kepala subbagian%' THEN 3
                ELSE 4
            END
        ");
    }

    /**
     * Handle large datasets using chunking for better performance
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function handleLargeDataset($query)
    {
        $totalRecords = $query->count();

        if ($totalRecords > 50) {
            // Use chunking for large datasets
            return $this->chunkParticipants($query);
        } else {
            // Get directly for smaller datasets
            return $query->limit(100)->get();
        }
    }

    /**
     * Chunk participants to handle large datasets efficiently
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function chunkParticipants($query)
    {
        $participants = collect();

        $query->chunk(25, function ($chunk) use (&$participants) {
            $participants = $participants->merge($chunk);

            // Limit to maximum 100 records
            if ($participants->count() >= 100) {
                return false; // Stop chunking
            }
        });

        return $participants->take(100);
    }

    /**
     * Get agenda filter information if applied
     *
     * @param Request $request
     * @return Agenda|null
     */
    private function getAgendaFilter(Request $request)
    {
        if ($request->filled('agenda_id')) {
            return Agenda::with('masterDinas')->find($request->agenda_id);
        }

        return null;
    }

    /**
     * Process logo file for PDF inclusion
     *
     * @return string|null Base64 encoded logo
     */
    private function processLogo()
    {
        $logoPathCandidates = [
            storage_path('app/public/Pemkot.png'),
            public_path('Pemkot.png'),
            resource_path('Pemkot.png'),
            base_path('Pemkot.png'),
        ];

        foreach ($logoPathCandidates as $candidate) {
            if (file_exists($candidate)) {
                try {
                    return 'data:image/png;base64,' . base64_encode(file_get_contents($candidate));
                } catch (\Throwable $e) {
                    // Continue to next candidate if this one fails
                    continue;
                }
            }
        }

        return null; // Return null if no logo found
    }

    /**
     * Configure and generate PDF
     *
     * @param \Illuminate\Database\Eloquent\Collection $participants
     * @param Agenda|null $agendaFilter
     * @param string|null $logoBase64
     * @return \Barryvdh\DomPDF\PDF
     */
    private function generatePdf($participants, $agendaFilter, $logoBase64)
    {
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled' => false,
            'dpi' => 72,
            'defaultFont' => 'sans-serif',
            'enable_php' => false,
            'chroot' => storage_path(),
            'enable_font_subsetting' => true,
            'pdf_backend' => 'CPDF',
        ])->loadView('admin.participants.export-pdf', compact('participants', 'agendaFilter', 'logoBase64'));

        return $pdf;
    }

    /**
     * Generate filename for the PDF export
     *
     * @return string
     */
    private function generateFilename()
    {
        return 'peserta_' . date('Y-m-d_H-i') . '.pdf';
    }
}
