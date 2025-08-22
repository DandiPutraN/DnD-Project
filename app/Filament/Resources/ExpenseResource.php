<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ExpenseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ExpenseResource\RelationManagers;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Others';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    Section::make([
                        Forms\Components\Select::make('name')
                            ->label('Akun Biaya')
                            ->options(
                                \App\Models\Account::query()
                                    ->where('kategori', '!=', 'Kas & Bank')
                                    ->get()
                                    ->mapWithKeys(function ($account) {
                                        return [
                                            '1-' . str_pad($account->id, 5, '0', STR_PAD_LEFT) . ' - ' . $account->nama 
                                            => '1-' . str_pad($account->id, 5, '0', STR_PAD_LEFT) . ' - ' . $account->nama
                                        ];
                                    })
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                            Forms\Components\DatePicker::make('date_expense')
                                ->required()
                                ->default(now())
                                ->displayFormat('d F Y')
                                ->weekStartsOnMonday()
                                ->locale('id')
                                ->native(false),
                        Forms\Components\Textarea::make('note')
                        ->columnSpanFull(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp.')
                            ->columnSpanFull()
                            ->minValue(0),                        
                    ])->columns(2)
                ])->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_expense')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->label('Setting'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
