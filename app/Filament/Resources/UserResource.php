<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Filament Shield';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Account')->schema([
                        TextInput::make('name')
                        ->required(),
                        
                        TextInput::make('email')
                        ->label('Email Adress')
                        ->email()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->required(),
    
                        DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At')
                        ->default(now()),
                        
                        TextInput::make('password')
                        ->password()
                        ->dehydrated(fn($state) => filled($state)),
                    ])->columns(2),
                ])->columnSpan(3),

                Group::make()->schema([
                    Section::make()->schema([
                    CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ])
                ])->columnSpan(1)

            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->modifyQueryUsing(function ($query) {
            //     return $query->whereDoesntHave('roles', function ($query) {
            //         $query->where('name', 'super_admin');
            //     });
            // })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                ->badge(),
                TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
