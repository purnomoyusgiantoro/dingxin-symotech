<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditDeliveryResource\Pages;
use App\Models\CreditDelivery;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CreditDeliveryResource extends Resource
{
    protected static ?string $model = CreditDelivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Faktur Kredit Toko';

    protected static ?string $modelLabel = 'Faktur Kredit';

    protected static ?string $pluralModelLabel = 'Pengiriman Kredit Toko';

    protected static ?string $navigationGroup = 'Operasional Kasir';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['cashier', 'gm', 'sales_admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rincian Faktur Kredit Toko')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal Pengiriman')
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

                        Forms\Components\TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->placeholder('Contoh: Toko Barokah Jaya')
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Faktur Kredit (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(1),

                        Forms\Components\FileUpload::make('invoice_photo_path')
                            ->label('Foto Fisik Faktur Penjualan')
                            ->image()
                            ->disk('public')
                            ->directory('credit_invoices')
                            ->openable()
                            ->downloadable()
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan / Keterangan')
                            ->placeholder('Contoh: Jatuh tempo 14 hari')
                            ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('store_name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal Kredit')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\ImageColumn::make('invoice_photo_path')
                    ->label('Foto Faktur')
                    ->disk('public')
                    ->circular()
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Keterangan')
                    ->limit(35),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListCreditDeliveries::route('/'),
            'create' => Pages\CreateCreditDelivery::route('/create'),
            'edit' => Pages\EditCreditDelivery::route('/{record}/edit'),
        ];
    }
}
