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

    public function viewAny(User $user): bool
    {
        $jabatan = $this->getJabatan($user);

        return in_array($jabatan, ['admin', 'teknisi'], true);
    }

    public function view(User $user, Laporan $laporan): bool
    {
        $jabatan = $this->getJabatan($user);
        if ($jabatan === 'admin') {
            return true;
        }
        if ($jabatan === 'teknisi') {
            return $laporan->karyawan_id === $this->getKaryawanId($user);
        }

        return false;
    }

    public function create(User $user): bool
    {
        $jabatan = $this->getJabatan($user);

        return in_array($jabatan, ['admin', 'teknisi'], true);
    }

    public function update(User $user, Laporan $laporan): bool
    {
        $jabatan = $this->getJabatan($user);
        if ($jabatan === 'admin') {
            return true;
        }

        if ($jabatan === 'teknisi') {
            return $laporan->karyawan_id === $this->getKaryawanId($user);
        }

        return false;
    }

    public function delete(User $user, Laporan $laporan): bool
    {
        $jabatan = $this->getJabatan($user);

        if ($jabatan === 'admin') {
            return true;
        }

        if ($jabatan === 'teknisi') {
            return $laporan->karyawan_id === $this->getKaryawanId($user);
        }

        return false;
    }

    public function restore(User $user, Laporan $laporan): bool
    {
        return $this->delete($user, $laporan);
    }

    public function forceDelete(User $user, Laporan $laporan): bool
    {
        return $this->delete($user, $laporan);
    }
}
