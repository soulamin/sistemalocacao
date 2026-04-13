<?php

namespace App\Filament\Resources\RentalResource\Pages;

use App\Filament\Resources\RentalResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewRentalReceipt extends Page
{
    use InteractsWithRecord;

    protected static string $resource = RentalResource::class;

    protected static string $view = 'filament.resources.rental-resource.pages.view-rental-receipt';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load(['client', 'products.category', 'tenant']);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Recibo '.$this->getRecord()->recibo_codigo;
    }

    public function getBreadcrumb(): string
    {
        return 'Recibo';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->url(RentalResource::getUrl('index')),
            Action::make('editar')
                ->label('Editar locação')
                ->url(fn (): string => RentalResource::getUrl('edit', ['record' => $this->getRecord()]))
                ->visible(fn (): bool => $this->getRecord()->status === 'ativa'),
        ];
    }
}
