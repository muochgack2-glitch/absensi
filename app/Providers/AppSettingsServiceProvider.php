<?php

namespace App\Providers;

use App\Models\AttendanceSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppSettingsServiceProvider extends ServiceProvider
{
    /**
     * Share sekolah settings ke semua view secara global.
     * Dipanggil setelah semua service provider lain boot.
     */
    public function boot(): void
    {
        // Guard: skip jika DB belum siap (misal saat migrate)
        try {
            $schoolName = AttendanceSetting::get('school_name', 'Sekolah');
            $schoolLogo = AttendanceSetting::get('school_logo');
            $logoUrl    = ($schoolLogo && Storage::disk('public')->exists($schoolLogo))
                ? Storage::disk('public')->url($schoolLogo)
                : null;

            View::share('appSchoolName', $schoolName);
            View::share('appLogoUrl',    $logoUrl);
        } catch (\Exception $e) {
            // DB not ready (e.g., fresh install) — share defaults
            View::share('appSchoolName', 'Sekolah');
            View::share('appLogoUrl',    null);
        }
    }
}
