<?php

namespace App\Filament\Resources\RentalResource\Pages;

use App\Filament\Resources\RentalResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRental extends CreateRecord
{
    protected static string $resource = RentalResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return RentalResource::createRental($data);
    }
}
