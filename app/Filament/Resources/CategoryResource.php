<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Categorias';

    protected static ?string $modelLabel = 'Categoria';

    protected static ?string $pluralModelLabel = 'Categorias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255)
                    ->rules([
                        fn (?Category $record): Unique => Rule::unique('categories', 'nome')
                            ->where(fn ($query) => $query->where('tenant_id', auth()->user()?->tenant_id))
                            ->ignore($record?->id),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label('Produtos')
                    ->counts('products')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Category $record): bool => $record->products()->exists()),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
