<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalResource\Pages;
use App\Models\Client;
use App\Models\Product;
use App\Models\Rental;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Locações';

    protected static ?string $modelLabel = 'Locação';

    protected static ?string $pluralModelLabel = 'Locações';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('client_id')
                    ->label('Cliente')
                    ->options(fn () => Client::orderBy('nome')->pluck('nome', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('empresa_responsavel')
                    ->label('Empresa responsável')
                    ->default(auth()->user()?->tenant?->nome)
                    ->required()
                    ->maxLength(255),
                DatePicker::make('data_inicio')
                    ->label('Data de início')
                    ->required(),
                DatePicker::make('data_fim')
                    ->label('Data de fim')
                    ->required()
                    ->afterOrEqual('data_inicio'),
                Select::make('product_ids')
                    ->label('Produtos')
                    ->options(fn () => Product::orderBy('nome')->get()->mapWithKeys(
                        fn (Product $product): array => [
                            $product->id => trim($product->cod_produto.' • '.$product->nome.' • '.$product->marca),
                        ],
                    ))
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->required()
                    ->dehydrated(true),
                TextInput::make('recibo_codigo')
                    ->label('Recibo')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recibo_codigo')
                    ->label('Recibo')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('client.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('empresa_responsavel')
                    ->label('Empresa')
                    ->searchable(),
                TextColumn::make('data_inicio')
                    ->label('Início')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('data_fim')
                    ->label('Fim')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('products_count')
                    ->label('Produtos')
                    ->counts('products')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('valor_total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => 'R$ '.number_format((float) $state, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'ativa' ? 'success' : 'gray'),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'ativa' => 'Ativa',
                        'finalizada' => 'Finalizada',
                    ]),
            ])
            ->actions([
                Action::make('recibo')
                    ->label('Recibo')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Rental $record): string => static::getUrl('receipt', ['record' => $record]))
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->visible(fn (Rental $record): bool => $record->status === 'ativa'),
                Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Rental $record): bool => $record->status === 'ativa')
                    ->requiresConfirmation()
                    ->action(fn (Rental $record) => static::finalizeRental($record)),
                DeleteAction::make()
                    ->action(fn (Rental $record) => static::deleteRental($record)),
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
            'index' => Pages\ListRentals::route('/'),
            'create' => Pages\CreateRental::route('/create'),
            'edit' => Pages\EditRental::route('/{record}/edit'),
            'receipt' => Pages\ViewRentalReceipt::route('/{record}/receipt'),
        ];
    }

    public static function createRental(array $data): Rental
    {
        $products = static::resolveProducts($data['product_ids'] ?? []);
        $total = static::calculateTotal($products, $data['data_inicio'], $data['data_fim']);

        return DB::transaction(function () use ($data, $products, $total): Rental {
            $rental = Rental::create([
                'client_id' => $data['client_id'],
                'empresa_responsavel' => $data['empresa_responsavel'],
                'data_inicio' => $data['data_inicio'],
                'data_fim' => $data['data_fim'],
                'valor_total' => $total,
                'status' => 'ativa',
            ]);

            $rental->update([
                'recibo_codigo' => 'REC-'.now()->format('Ymd').'-'.str_pad((string) $rental->id, 5, '0', STR_PAD_LEFT),
            ]);

            static::syncProducts($rental, $products);

            return $rental;
        });
    }

    public static function updateRental(Rental $rental, array $data): Rental
    {
        $products = static::resolveProducts($data['product_ids'] ?? [], $rental);
        $total = static::calculateTotal($products, $data['data_inicio'], $data['data_fim']);
        $currentProductIds = $rental->products()->pluck('products.id');

        return DB::transaction(function () use ($rental, $data, $products, $total, $currentProductIds): Rental {
            $rental->update([
                'client_id' => $data['client_id'],
                'empresa_responsavel' => $data['empresa_responsavel'],
                'data_inicio' => $data['data_inicio'],
                'data_fim' => $data['data_fim'],
                'valor_total' => $total,
            ]);

            Product::whereIn('id', $currentProductIds)->update(['status' => 'disponivel']);
            static::syncProducts($rental, $products);

            return $rental->refresh();
        });
    }

    public static function finalizeRental(Rental $rental): void
    {
        if ($rental->status === 'finalizada') {
            return;
        }

        DB::transaction(function () use ($rental): void {
            $rental->update(['status' => 'finalizada']);
            Product::whereIn('id', $rental->products()->pluck('products.id'))->update(['status' => 'disponivel']);
        });
    }

    public static function deleteRental(Rental $rental): void
    {
        DB::transaction(function () use ($rental): void {
            Product::whereIn('id', $rental->products()->pluck('products.id'))->update(['status' => 'disponivel']);
            $rental->products()->detach();
            $rental->delete();
        });
    }

    protected static function resolveProducts(array $productIds, ?Rental $rental = null): Collection
    {
        $productIds = collect($productIds)
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($productIds === []) {
            throw ValidationException::withMessages([
                'product_ids' => ['Selecione ao menos um produto.'],
            ]);
        }

        $products = Product::whereIn('id', $productIds)->get();

        if ($products->count() !== count($productIds)) {
            throw ValidationException::withMessages([
                'product_ids' => ['Um ou mais produtos são inválidos.'],
            ]);
        }

        $currentProductIds = $rental?->products()->pluck('products.id')->all() ?? [];

        $hasUnavailableProducts = $products->contains(
            fn (Product $product): bool => $product->status !== 'disponivel' && ! in_array($product->id, $currentProductIds, true)
        );

        if ($hasUnavailableProducts) {
            throw ValidationException::withMessages([
                'product_ids' => ['Existem produtos já locados.'],
            ]);
        }

        return $products;
    }

    protected static function syncProducts(Rental $rental, Collection $products): void
    {
        $syncData = [];

        foreach ($products as $product) {
            $syncData[$product->id] = [
                'tenant_id' => auth()->user()?->tenant_id,
                'valor_diaria' => $product->valor_diaria,
            ];
        }

        $rental->products()->sync($syncData);
        Product::whereIn('id', $products->pluck('id'))->update(['status' => 'locado']);
    }

    protected static function calculateTotal(Collection $products, string $startDate, string $endDate): float
    {
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        return (float) $products->sum(fn (Product $product): float => (float) $product->valor_diaria * $days);
    }
}
