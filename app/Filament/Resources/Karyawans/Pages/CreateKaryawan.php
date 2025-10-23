<?php

namespace App\Filament\Resources\Karyawans\Pages;

use App\Filament\Resources\Karyawans\KaryawanResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateKaryawan extends CreateRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Create associated user using email & password from the form
        $user = User::query()->create([
            'name' => $data['nama'] ?? 'User',
            'email' => $data['email'],
            'password' => $data['password'], // Will be hashed by User model cast
        ]);

        // Attach created user to karyawan payload
        $data['user_id'] = $user->id;

        // Remove account fields from Karyawan payload
        unset($data['email'], $data['password']);

        return $data;
    }
}
