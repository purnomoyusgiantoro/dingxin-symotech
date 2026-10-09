<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Services\SettlementCalculationService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GmExecutiveDashboard extends BaseWidget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'gm';
    }

    protected function getStats(): array
    {
        $service = app(SettlementCalculationService::class);
        $data = $service->calculateAllDriversDaily(Carbon::today()->format('Y-m-d'));
        $summary = $data['summary_all'];

        $activeDrivers = Driver::where('is_active', true)->count();

        return [
            Stat::make('Armada Aktif', $activeDrivers)
                ->description('Total sopir terdaftar aktif')
                ->icon('heroicon-o-truck')
                ->color('primary'),

            Stat::make('Total Bawaan Hari Ini', 'Rp ' . number_format($summary['total_carried'], 0, ',', '.'))
                ->description('Nilai barang seluruh armada')
                ->icon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Total Sudah Setor', 'Rp ' . number_format($summary['total_sudah_setor'], 0, ',', '.'))
                ->description('Uang tunai + transfer approved')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Total Selisih', 'Rp ' . number_format(abs($summary['total_selisih_setor']), 0, ',', '.'))
                ->description($summary['total_selisih_setor'] > 0 ? 'Kurang setor' : ($summary['total_selisih_setor'] < 0 ? 'Lebih setor' : 'Lunas semua'))
                ->icon('heroicon-o-scale')
                ->color($summary['total_selisih_setor'] > 0 ? 'danger' : 'success'),
        ];
    }
}
