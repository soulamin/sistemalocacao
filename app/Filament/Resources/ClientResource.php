<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nome')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->required()
                    ->mask(RawJs::make(<<<'JS'
                        $input.replace(/\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-9999'
                    JS))
                    ->maxLength(16),
                TextInput::make('documento')
                    ->label('CPF/CNPJ')
                    ->required()
                    ->mask(RawJs::make(<<<'JS'
                        $input.replace(/\D/g, '').length > 11 ? '99.999.999/9999-99' : '999.999.999-99'
                    JS))
                    ->maxLength(20)
                    ->helperText('Informe CPF ou CNPJ.')
                    ->rules([
                        fn (?Client $record): Unique => Rule::unique('clients', 'documento')
                            ->where(fn ($query) => $query->where('tenant_id', auth()->user()?->tenant_id))
                            ->ignore($record?->id),
                    ]),
                Grid::make(4)
                    ->schema([
                        TextInput::make('cep')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->maxLength(9)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                $cep = preg_replace('/\D/', '', (string) $state);

                                if (strlen($cep) !== 8) {
                                    return;
                                }

                                $response = Http::timeout(5)->get("https://viacep.com.br/ws/{$cep}/json/");

                                if (! $response->ok()) {
                                    return;
                                }

                                $address = $response->json();

                                if (($address['erro'] ?? false) === true) {
                                    return;
                                }

                                $set('endereco', $address['logradouro'] ?? '');
                                $set('bairro', $address['bairro'] ?? '');
                                $set('cidade', $address['localidade'] ?? '');
                                $set('uf', $address['uf'] ?? '');
                                $set('complemento', $address['complemento'] ?? '');
                            }),
                        TextInput::make('uf')
                            ->label('UF')
                            ->maxLength(2),
                        TextInput::make('cidade')
                            ->label('Cidade')
                            ->maxLength(120),
                        TextInput::make('bairro')
                            ->label('Bairro')
                            ->maxLength(120),
                    ]),
                TextInput::make('endereco')
                    ->label('Endereço')
                    ->maxLength(255),
                Grid::make(3)
                    ->schema([
                        TextInput::make('numero')
                            ->label('Número')
                            ->maxLength(20),
                        TextInput::make('complemento')
                            ->label('Complemento')
                            ->maxLength(120),
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
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('telefone')
                    ->label('Telefone')
                    ->searchable(),
                TextColumn::make('documento')
                    ->label('CPF/CNPJ')
                    ->searchable(),
                TextColumn::make('cidade')
                    ->label('Cidade')
                    ->toggleable(),
                TextColumn::make('uf')
                    ->label('UF')
                    ->toggleable(),
                TextColumn::make('rentals_count')
                    ->label('Locações')
                    ->counts('rentals')
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
                    ->disabled(fn (Client $record): bool => $record->rentals()->where('status', 'ativa')->exists()),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
