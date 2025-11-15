<?php

namespace App\Policies;

use App\Models\PiutangProdukCicilanDetail;
use App\Models\User;

class PiutangProdukCicilanDetailPolicy
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
    public function view(User $user, PiutangProdukCicilanDetail $piutangProdukCicilanDetail): bool
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
    public function update(User $user, PiutangProdukCicilanDetail $piutangProdukCicilanDetail): bool
    {
        $jabatan = $this->getJabatan($user);

        return $jabatan === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PiutangProdukCicilanDetail $piutangProdukCicilanDetail): bool
    {
        $jabatan = $this->getJabatan($user);

        return $jabatan === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PiutangProdukCicilanDetail $piutangProdukCicilanDetail): bool
    {
        return $this->delete($user, $piutangProdukCicilanDetail);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PiutangProdukCicilanDetail $piutangProdukCicilanDetail): bool
    {
        return $this->delete($user, $piutangProdukCicilanDetail);
    }
}
