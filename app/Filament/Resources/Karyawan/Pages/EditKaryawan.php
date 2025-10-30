<?php

namespace App\Filament\Resources\Karyawan\Pages;

use App\Filament\Resources\Karyawan\KaryawanResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;

class EditKaryawan extends EditRecord
{
    protected static string $resource = KaryawanResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = $this->record->user;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if ($user) {
            if ($email && $email !== $user->email) {
                $user->email = $email;
            }

            if ($password) {
                // Password will be hashed by User model casts
                $user->password = $password;
            }

            $user->save();
        } else {
            // If the record has no user yet, create and associate one
            if ($email) {
                $user = User::query()->create([
                    'name' => $data['nama'] ?? 'User',
                    'email' => $email,
                    'password' => $password ?? 'password', // hashed by casts
                ]);

                $this->record->user()->associate($user);
                $this->record->save();
            }
        }

        // Remove account fields from Karyawan payload to avoid unexpected fill attempts
        unset($data['email'], $data['password']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make()
                ->requiresConfirmation()
                ->form([
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->rules(['unique:users,email'])
                        ->required(),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->minLength(6)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    // Restore the Karyawan record first
                    $this->record->restore();

                    // Create a fresh User and associate with the restored Karyawan
                    $user = User::query()->create([
                        'name' => $this->record->nama ?? 'User',
                        'email' => $data['email'],
                        'password' => $data['password'], // hashed by User model casts
                    ]);

                    $this->record->user()->associate($user);
                    $this->record->save();
                })
                ->successNotificationTitle('Karyawan berhasil di-restore dan akun pengguna dibuat ulang'),
        ];
    }
}
