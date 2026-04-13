<?php

namespace App\Filament\Resources\RentalResource\Pages;

use App\Filament\Resources\RentalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRental extends EditRecord
{
    protected static string $resource = RentalResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['product_ids'] = $this->record->products()->pluck('products.id')->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return RentalResource::updateRental($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function (): void {
                    RentalResource::deleteRental($this->record);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
