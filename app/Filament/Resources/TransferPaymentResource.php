<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferPaymentResource\Pages;
use App\Models\TransferPayment;
use App\Services\SettlementCalculationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransferPaymentResource extends Resource
{
    protected static ?string $model = TransferPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Konfirmasi Transfer';

    protected static ?string $modelLabel = 'Transfer Toko';

    protected static ?string $pluralModelLabel = 'Konfirmasi Transfer Toko';

    protected static ?string $navigationGroup = 'Operasional Kasir';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['cashier', 'gm', 'sales_admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rincian Transfer Toko')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal Transfer')
                            ->disabled(),

                        Forms\Components\TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->required(),

                        Forms\Components\TextInput::make('claimed_amount')
                            ->label('Nominal Klaim Sopir (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\TextInput::make('verified_amount')
                            ->label('Nominal Diverifikasi Kasir (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Isi sesuai mutasi riil di rekening koran kasir'),

                        Forms\Components\Select::make('status')
                            ->label('Status Verifikasi')
                            ->options([
                                'pending' => 'Pending (Menunggu)',
                                'approved' => 'Approved (Diterima)',
                                'rejected' => 'Rejected (Ditolak)',
                            ])
                            ->required(),

                        Forms\Components\FileUpload::make('proof_image_path')
                            ->label('Foto Bukti Transfer')
                            ->image()
                            ->disk('public')
                            ->directory('proofs/transfers')
                            ->openable()
                            ->downloadable(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Mutasi Bank')
                            ->placeholder('Contoh: Mutasi rekening BCA an Toko Berkah')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('rejection_reason')
                            ->label('Alasan Penolakan (Jika ditolak)')
                            ->placeholder('Contoh: Mutasi belum masuk atau bukti tidak valid')
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

                Tables\Columns\TextColumn::make('claimed_amount')
                    ->label('Klaim Sopir')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('verified_amount')
                    ->label('Nominal Disetujui')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-'),

                Tables\Columns\ImageColumn::make('proof_image_path')
                    ->label('Bukti')
                    ->disk('public')
                    ->circular()
                    ->openUrlInNewTab(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Kasir Verifikator')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending (Menunggu)',
                        'approved' => 'Approved (Diterima)',
                        'rejected' => 'Rejected (Ditolak)',
                    ]),
                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn ($query) => $query->whereDate('date', today())),
            ])
            ->actions([
                // Aksi Kasir: Setujui Transfer (Approve)
                Tables\Actions\Action::make('approveTransfer')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (TransferPayment $record) => $record->status !== 'approved')
                    ->form([
                        Forms\Components\TextInput::make('verified_amount')
                            ->label('Nominal Mutasi Bank yang Masuk (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn (TransferPayment $record) => $record->claimed_amount)
                            ->required()
                            ->helperText('Bisa disesuaikan bila nominal mutasi bank ada selisih dengan klaim sopir.'),

                        Forms\Components\TextInput::make('notes')
                            ->label('Catatan Mutasi (Opsional)')
                            ->placeholder('Contoh: Rekening BCA masuk valid'),
                    ])
                    ->action(function (TransferPayment $record, array $data, SettlementCalculationService $calc): void {
                        $record->update([
                            'status' => 'approved',
                            'verified_amount' => $data['verified_amount'],
                            'notes' => $data['notes'] ?? $record->notes,
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        // Refresh kalkulasi harian
                        $calc->calculateDriverDaily($record->driver_id, $record->date);

                        Notification::make()
                            ->title('Transfer Dikonfirmasi')
                            ->body("Transfer Rp " . number_format($data['verified_amount'], 0, ',', '.') . " untuk toko {$record->store_name} berhasil disetujui.")
                            ->success()
                            ->send();
                    }),

                // Aksi Kasir: Tolak Transfer (Reject)
                Tables\Actions\Action::make('rejectTransfer')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (TransferPayment $record) => $record->status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Contoh: Belum ada mutasi masuk di rekening koran kasir'),
                    ])
                    ->action(function (TransferPayment $record, array $data, SettlementCalculationService $calc): void {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        // Refresh kalkulasi harian
                        $calc->calculateDriverDaily($record->driver_id, $record->date);

                        Notification::make()
                            ->title('Transfer Ditolak')
                            ->body("Klaim transfer untuk toko {$record->store_name} telah ditolak.")
                            ->danger()
                            ->send();
                    }),

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
            'index' => Pages\ListTransferPayments::route('/'),
            'edit' => Pages\EditTransferPayment::route('/{record}/edit'),
        ];
    }
}
