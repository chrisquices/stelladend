<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function upgradeToPremium()
    {
        $user = auth()->user();
        $user->is_premium = true;
        $user->save();
    }
}
