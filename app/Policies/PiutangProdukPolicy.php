<?php

namespace App\Policies;

use App\Models\PiutangProduk;
use App\Models\User;

class PiutangProdukPolicy
{
    protected function getJabatan(User $user): ?string
    {
        return $user->karyawan->jabatan ?? null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $jabatan = $this->getJabatan($user);

        return $jabatan === 'admin' || $jabatan === 'staff';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PiutangProduk $piutangProduk): bool
    {
        $jabatan = $this->getJabatan($user);

        return $jabatan === 'admin' || $jabatan === 'staff';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $jabatan = $this->getJabatan($user);

        return $jabatan === 'admin' || $jabatan === 'staff';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PiutangProduk $piutangProduk): bool
    {
        $jabatan = $this->getJabatan($user);

        return $jabatan === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PiutangProduk $piutangProduk): bool
    {
        $jabatan = $this->getJabatan($user);

        return $jabatan === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PiutangProduk $piutangProduk): bool
    {
        return $this->delete($user, $piutangProduk);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PiutangProduk $piutangProduk): bool
    {
        return $this->delete($user, $piutangProduk);
    }
}
