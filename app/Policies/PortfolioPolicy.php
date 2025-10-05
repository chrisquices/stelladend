<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;

class PortfolioPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Portfolio $portfolio): bool
    {
        return $user->id === $portfolio->user_id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Portfolio $portfolio): bool
    {
        return false;
    }

    public function delete(User $user, Portfolio $portfolio): bool
    {
        return false;
    }

    public function restore(User $user, Portfolio $portfolio): bool
    {
        return false;
    }

    public function forceDelete(User $user, Portfolio $portfolio): bool
    {
        return false;
    }
}
