<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Menerapkan filter tanggal pada query
     */
    protected function applyDateFilters(Builder $query, Request $request, string $dateColumn = 'created_at'): Builder
    {
        if ($request->filled('tanggal')) {
            $query->whereDay($dateColumn, $request->tanggal);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth($dateColumn, $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear($dateColumn, $request->tahun);
        }

        return $query;
    }

    /**
     * Menerapkan filter agenda pada query
     */
    protected function applyAgendaFilter(Builder $query, Request $request): Builder
    {
        if ($request->filled('agenda_id')) {
            $query->where('agenda_id', $request->agenda_id);
        }

        return $query;
    }

    /**
     * Menerapkan filter umum untuk query terkait agenda
     */
    protected function applyAgendaDateFilters(Builder $query, Request $request): Builder
    {
        return $query->whereHas('agenda', function($q) use ($request) {
            $this->applyDateFilters($q, $request, 'tanggal_agenda');
        });
    }
}
