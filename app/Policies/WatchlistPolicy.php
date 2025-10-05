<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Auth\Access\Response;

class WatchlistPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Watchlist $watchlist): bool
    {
        return $user->id === $watchlist->user_id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Watchlist $watchlist): bool
    {
        return false;
    }

    public function delete(User $user, Watchlist $watchlist): bool
    {
        return false;
    }

    public function restore(User $user, Watchlist $watchlist): bool
    {
        return false;
    }

    public function forceDelete(User $user, Watchlist $watchlist): bool
    {
        return false;
    }
}
