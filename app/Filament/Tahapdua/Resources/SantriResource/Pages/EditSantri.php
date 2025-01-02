<?php

namespace App\Filament\Tahapdua\Resources\SantriResource\Pages;

use App\Filament\Tahapdua\Resources\SantriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSantri extends EditRecord
{
    protected static string $resource = SantriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
