<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;

class AbsensiPolicy
{
    protected function getJabatan(User $user): ?string
    {
        return $user->karyawan->jabatan ?? null;
    }

    protected function getKaryawanId(User $user): ?int
    {
        return $user->karyawan->id ?? null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->getJabatan($user) === 'admin' || $this->getJabatan($user) === 'staff';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Absensi $absensi): bool
    {
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'admin') {
            return true;
        }

        return $absensi->karyawan_id === $this->getKaryawanId($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Absensi $absensi): bool
    {
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'admin') {
            return true;
        }

        return $absensi->karyawan_id === $this->getKaryawanId($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Absensi $absensi): bool
    {
        return $this->getJabatan($user) === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Absensi $absensi): bool
    {
        return $this->getJabatan($user) === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Absensi $absensi): bool
    {
        return $this->getJabatan($user) === 'admin';
    }
}
