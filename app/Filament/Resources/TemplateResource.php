<?php

namespace App\Filament\Resources;

use App\Filament\Imports\TemplateAsetImporter;
use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Models\Template;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\AsetCluster::class;

    protected static ?string $model = Template::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Template Aset';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Template Aset';
    protected static ?int $navigationSort = 91;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Select::make('category_id')
                ->label('Kategori')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nama Kategori')
                        ->required(),

                    TextInput::make('code')
                        ->label('Kode Kategori')
                        ->required(),
                ]),

            TextInput::make('name')->required()->maxLength(50),
            TextInput::make('code')->required()->maxLength(50),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('category.name')->label('Kategori')->sortable()->searchable(),
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('code')->sortable()->searchable(),
        ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->tooltip('Edit'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(TemplateAsetImporter::class)
                    ->modalHeading('Import Data Template Aset')
                    ->modalDescription('Pastikan file sesuai dengan format yang ditentukan.')
                    ->modalSubmitActionLabel('Import Data'),

                Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn() => response()->download(storage_path('app/public/template_aset-import-template.xlsx')))
                    ->color('secondary'),
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view templates');
    }
}
