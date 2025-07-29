<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // Contoh: hanya admin yang bisa melihat daftar user
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Bisa melihat dirinya sendiri atau jika admin
        return $user->id === $model->id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        // Hanya admin yang boleh membuat user baru
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin boleh update semua, atau user update dirinya sendiri
        return $user->id === $model->id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin bisa menghapus user lain, tapi tidak bisa hapus dirinya sendiri
        return $user->role === 'admin' && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
