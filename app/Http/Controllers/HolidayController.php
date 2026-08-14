<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Services\EkaldikHolidayService;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Display list of holidays.
     */
    public function index()
    {
        $holidays = Holiday::orderBy('start_date', 'desc')->get();
        $lastSync = Holiday::fromEkaldik()->max('synced_at');

        return view('holidays.index', compact('holidays', 'lastSync'));
    }

    /**
     * Store a manually created holiday.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        Holiday::create([
            ...$validated,
            'source' => 'manual',
            'is_active' => true,
        ]);

        return redirect()->route('holidays.index')
            ->with('success', 'Hari libur berhasil ditambahkan');
    }

    /**
     * Delete a holiday.
     */
    public function destroy(Holiday $holiday)
    {
        $name = $holiday->name;
        $holiday->delete();

        return redirect()->route('holidays.index')
            ->with('success', "Hari libur \"{$name}\" berhasil dihapus");
    }

    /**
     * Sync holidays from E-Kaldik.
     */
    public function sync(EkaldikHolidayService $service)
    {
        $stats = $service->syncFromEkaldik();

        if (!empty($stats['errors'])) {
            return redirect()->route('holidays.index')
                ->with('error', 'Sync gagal: ' . implode(', ', $stats['errors']));
        }

        $msg = "Sync berhasil! Ditambahkan: {$stats['added']}, Diperbarui: {$stats['updated']}, Dihapus: {$stats['removed']}";

        return redirect()->route('holidays.index')
            ->with('success', $msg);
    }
}
