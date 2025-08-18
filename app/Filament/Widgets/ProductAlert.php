<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Product;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductAlert extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Product of less than 10 items';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->where('stock', '<=', 10)->orderBy('stock', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\BadgeColumn::make('stock')
                ->label('Stock')
                ->numeric()
                ->color(static function ($state) {
                    if ($state < 5) {
                        return 'danger';
                    } elseif ($state <= 10) {
                        return 'warning';
                    }
                    
                })
                ->sortable()
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
