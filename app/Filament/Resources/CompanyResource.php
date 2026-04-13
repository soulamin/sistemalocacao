<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Tenant;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View as FormView;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CompanyResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Empresa';

    protected static ?string $modelLabel = 'Empresa responsável';

    protected static ?string $pluralModelLabel = 'Empresa responsável';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados da empresa')
                    ->schema([
                        TextInput::make('documento')
                            ->label('CNPJ')
                            ->required()
                            ->mask('99.999.999/9999-99')
                            ->maxLength(18)
                            ->extraInputAttributes(['id' => 'empresa-cnpj'])
                            ->hint(new HtmlString('<button type="button" id="buscar-cnpj-empresa" class="text-sm font-semibold text-primary-600 dark:text-primary-400">Buscar CNPJ</button>')),
                        TextInput::make('nome')
                            ->label('Nome da empresa')
                            ->required()
                            ->maxLength(255)
                            ->extraInputAttributes(['id' => 'empresa-nome']),
                        TextInput::make('telefone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(30)
                            ->extraInputAttributes(['id' => 'empresa-telefone']),
                    ])
                    ->columns(2),
                Section::make('Endereço')
                    ->schema([
                        TextInput::make('cep')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->maxLength(9)
                            ->extraInputAttributes(['id' => 'empresa-cep'])
                            ->hint(new HtmlString('<button type="button" id="buscar-cep-empresa" class="text-sm font-semibold text-primary-600 dark:text-primary-400">Buscar CEP</button>')),
                        TextInput::make('endereco')
                            ->label('Endereço')
                            ->maxLength(255)
                            ->extraInputAttributes(['id' => 'empresa-endereco']),
                        TextInput::make('numero')
                            ->label('Número')
                            ->maxLength(20)
                            ->extraInputAttributes(['id' => 'empresa-numero']),
                        TextInput::make('complemento')
                            ->label('Complemento')
                            ->maxLength(255)
                            ->extraInputAttributes(['id' => 'empresa-complemento']),
                        TextInput::make('bairro')
                            ->label('Bairro')
                            ->maxLength(255)
                            ->extraInputAttributes(['id' => 'empresa-bairro']),
                        TextInput::make('cidade')
                            ->label('Cidade')
                            ->maxLength(255)
                            ->extraInputAttributes(['id' => 'empresa-cidade']),
                        TextInput::make('uf')
                            ->label('UF')
                            ->maxLength(2)
                            ->extraInputAttributes(['id' => 'empresa-uf'])
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? mb_strtoupper($state) : null),
                    ])
                    ->columns(2),
                FormView::make('filament.forms.company-search-scripts')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nome')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('documento')
                    ->label('CNPJ')
                    ->searchable(),
                TextColumn::make('cidade')
                    ->label('Cidade')
                    ->searchable(),
                TextColumn::make('uf')
                    ->label('UF')
                    ->badge(),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereKey(auth()->user()?->tenant_id);
    }

    public static function getNavigationUrl(): string
    {
        $tenantId = auth()->user()?->tenant_id;

        if ($tenantId) {
            return static::getUrl('edit', ['record' => $tenantId]);
        }

        return static::getUrl('index');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
