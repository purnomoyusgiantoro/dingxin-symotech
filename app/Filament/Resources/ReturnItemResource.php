<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnItemResource\Pages;
use App\Models\Driver;
use App\Models\ReturnItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReturnItemResource extends Resource
{
    protected static ?string $model = ReturnItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationLabel = 'Input Barang Kembali';

    protected static ?string $modelLabel = 'Barang Kembali (Retur)';

    protected static ?string $pluralModelLabel = 'Barang Kembali Per Sopir';

    protected static ?string $navigationGroup = 'Operasional Penjualan';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['sales_admin', 'gm', 'cashier']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Barang Kembali / Retur Sopir')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal Retur')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('driver_id')
                            ->label('Sopir Armada')
                            ->options(function () {
                                return Driver::with('user')
                                    ->where('is_active', true)
                                    ->get()
                                    ->mapWithKeys(function ($d) {
                                        return [$d->id => "{$d->driver_code} - " . ($d->user?->name ?? 'Sopir')];
                                    });
                            })
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Barang Kembali (Rupiah)')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('500000')
                            ->required()
                            ->minValue(1),

                        Forms\Components\Textarea::make('notes')
                            ->label('Keterangan / Alasan Retur')
                            ->placeholder('Contoh: Dus penyok, kemasan bocor, toko tutup')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('driver.driver_code')
                    ->label('Kode Sopir')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('driver.user.name')
                    ->label('Nama Sopir')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal Retur')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Keterangan')
                    ->limit(40),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Diinput Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn ($query) => $query->whereDate('date', today())),
                Tables\Filters\SelectFilter::make('driver_id')
                    ->label('Filter Sopir')
                    ->relationship('driver', 'driver_code'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnItems::route('/'),
            'create' => Pages\CreateReturnItem::route('/create'),
            'edit' => Pages\EditReturnItem::route('/{record}/edit'),
        ];
    }
}
