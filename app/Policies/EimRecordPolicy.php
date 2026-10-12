<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class EimRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageRecords();
    }

    public function view(User $user, Model $record): bool
    {
        return $user->canManageRecords();
    }

    public function create(User $user): bool
    {
        return $user->canManageRecords();
    }

    public function update(User $user, Model $record): bool
    {
        return $user->canPublishRecords() || ($user->role === User::ROLE_DATA_ENTRY && $record->status === 'draft');
    }

    public function delete(User $user, Model $record): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
