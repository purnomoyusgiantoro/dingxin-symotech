<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Master Sopir';

    protected static ?string $modelLabel = 'Sopir';

    protected static ?string $pluralModelLabel = 'Master Sopir';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['sales_admin', 'gm', 'cashier']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun & Armada Sopir')
                    ->schema([
                        Forms\Components\TextInput::make('driver_code')
                            ->label('Kode Sopir')
                            ->placeholder('Contoh: TGL1.2 atau BRS1.2')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (?Driver $record) => $record !== null),

                        Forms\Components\Select::make('area_code')
                            ->label('Wilayah Operasional')
                            ->options([
                                'TGL' => 'Tegal (TGL)',
                                'BRS' => 'Brebes (BRS)',
                                'OTHER' => 'Lainnya',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Sopir')
                            ->required()
                            ->formatStateUsing(fn (?Driver $record) => $record?->user?->name)
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('Nomor WhatsApp / HP')
                            ->tel()
                            ->placeholder('0812xxxxxxxx'),

                        Forms\Components\TextInput::make('plate_number')
                            ->label('Nomor Polisi Armada')
                            ->placeholder('Contoh: G 8123 EZ'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('driver_code')
                    ->label('Kode Sopir')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('area_code')
                    ->label('Area')
                    ->colors([
                        'primary' => 'TGL',
                        'warning' => 'BRS',
                        'secondary' => 'OTHER',
                    ]),

                Tables\Columns\TextColumn::make('plate_number')
                    ->label('No. Plat')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No. HP')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('cumulative_balance')
                    ->label('Saldo Kumulatif')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('area_code')
                    ->label('Filter Area')
                    ->options([
                        'TGL' => 'Tegal',
                        'BRS' => 'Brebes',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
