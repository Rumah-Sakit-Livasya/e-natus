<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\Pages\EmployeeAttendanceReport;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pegawai';
    protected static ?string $navigationGroup = 'User Management';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\DatePicker::make('birth_date')
                ->label('Tanggal Lahir')
                ->native(false)
                ->displayFormat('d F Y')
                ->required(),

            TextInput::make('nik')
                ->label('NIK')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('position')
                ->label('Jabatan')
                ->required(),

            TextInput::make('phone')
                ->label('No. HP')
                ->required(),

            TextInput::make('address')
                ->label('Alamat')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Nama User')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->date('d F Y'),
                TextColumn::make('nik')->sortable()->searchable(),
                TextColumn::make('position')->label('Jabatan'),
                TextColumn::make('phone'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),

            // SEKARANG INI AKAN BEKERJA KARENA BASE CLASS-NYA SUDAH BENAR
            'report' => Pages\EmployeeAttendanceReport::route('/report'),

            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view employees');
    }
}
