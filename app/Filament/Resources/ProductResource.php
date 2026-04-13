<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $modelLabel = 'Produto';

    protected static ?string $pluralModelLabel = 'Produtos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('cod_produto')
                    ->label('Código')
                    ->required()
                    ->maxLength(60)
                    ->rules([
                        fn (?Product $record): Unique => Rule::unique('products', 'cod_produto')
                            ->where(fn ($query) => $query->where('tenant_id', auth()->user()?->tenant_id))
                            ->ignore($record?->id),
                    ]),
                TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('marca')
                    ->label('Marca')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label('Categoria')
                    ->options(fn () => Category::orderBy('nome')->pluck('nome', 'id'))
                    ->searchable()
                    ->preload(),
                Textarea::make('descricao')
                    ->label('Descrição')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('valor_diaria')
                    ->label('Valor da diária')
                    ->required()
                    ->prefix('R$')
                    ->mask(RawJs::make(<<<'JS'
                        $money($input, ',', '.', 2)
                    JS))
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, ',', '.'))
                    ->dehydrateStateUsing(
                        fn (?string $state): float => (float) str_replace(',', '.', str_replace('.', '', (string) $state))
                    ),
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'disponivel' => 'Disponível',
                        'locado' => 'Locado',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cod_produto')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('marca')
                    ->label('Marca')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.nome')
                    ->label('Categoria')
                    ->placeholder('Sem categoria')
                    ->sortable(),
                TextColumn::make('valor_diaria')
                    ->label('Diária')
                    ->formatStateUsing(fn ($state): string => 'R$ '.number_format((float) $state, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'disponivel' ? 'success' : 'warning'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'disponivel' => 'Disponível',
                        'locado' => 'Locado',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Product $record): bool => $record->status === 'locado' || $record->hasActiveRental()),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
