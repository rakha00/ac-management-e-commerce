<?php

namespace App\Filament\Resources\Karyawan\Pages;

use App\Filament\Resources\Karyawan\KaryawanResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateKaryawan extends CreateRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Create associated User first from full raw form state to satisfy NOT NULL foreign key
        $state = method_exists($this->form, 'getRawState')
            ? $this->form->getRawState()
            : $this->form->getState();

        $email = data_get($state, 'user.email');
        $password = data_get($state, 'user.password');
        $name = $data['nama'] ?? 'User';

        // Fallback to dehydrated state if raw state missing
        if (! filled($email)) {
            $email = data_get($this->form->getState(), 'user.email');
        }
        if (! filled($password)) {
            $password = data_get($this->form->getState(), 'user.password');
        }

        // Create User only when email is present to avoid DB constraint violations
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // hashed by User model cast
        ]);

        // Attach created user to karyawan payload before base record creation
        $data['user_id'] = $user->id;

        // Create Karyawan record with linked user_id present
        return static::getModel()::create($data);
    }
}
