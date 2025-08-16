<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Product;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class FavouriteProducts extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Favourite Products';
    public function table(Table $table): Table
    {
        return $table
            ->query(
        Product::query()
                ->withCount('orderProducts')
                ->orderByDesc('order_products_count')
                ->take(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\TextColumn::make('order_products_count')
                ->label('Total Ordered Product'),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadGambar')
                ->label('Gambar')
                ->icon('heroicon-o-photo')
                ->url(fn ($record) => url('storage/' . strtoupper($record->image)))
                ->openUrlInNewTab()
                ->visible(fn ($record) => !empty($record->image)),
            ])
            ->defaultPaginationPageOption(5);
    }
}
