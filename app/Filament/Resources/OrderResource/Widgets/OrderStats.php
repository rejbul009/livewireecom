<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
        Stat::make('New Orders', Order::query()->where('status', 'new')->count())
            ->color('success')
            ->icon('heroicon-o-shopping-cart'),

            Stat::make('Order Processing', Order::query()->where('status', 'procesing')->count())
            ->color('success')
            ->icon('heroicon-o-shopping-cart'),

            Stat::make('Order Shiped', Order::query()->where('status', 'shiped')->count())
            ->color('success')
            ->icon('heroicon-o-shopping-cart'),
        ];
    }
}
