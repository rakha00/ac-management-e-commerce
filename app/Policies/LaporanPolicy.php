<?php

namespace App\Policies;

use App\Models\Laporan;
use App\Models\User;

class LaporanPolicy
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
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'sales' || $jabatan === 'gudang') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Laporan $laporan): bool
    {
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'sales' || $jabatan === 'gudang') {
            return false;
        }

        if ($jabatan === 'admin') {
            return true;
        }

        if ($jabatan !== 'admin') {
            return $laporan->karyawan_id === $this->getKaryawanId($user);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'sales' || $jabatan === 'gudang') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Laporan $laporan): bool
    {
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'sales' || $jabatan === 'gudang') {
            return false;
        }

        if ($jabatan === 'admin') {
            return true;
        }

        if ($jabatan !== 'admin') {
            return $laporan->karyawan_id === $this->getKaryawanId($user);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Laporan $laporan): bool
    {
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'sales' || $jabatan === 'gudang') {
            return false;
        }

        if ($jabatan === 'admin') {
            return true;
        }

        if ($jabatan !== 'admin') {
            return $laporan->karyawan_id === $this->getKaryawanId($user);
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Laporan $laporan): bool
    {
        return $this->delete($user, $laporan);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Laporan $laporan): bool
    {
        return $this->delete($user, $laporan);
    }
}
