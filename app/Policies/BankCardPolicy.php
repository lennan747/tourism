<?php

namespace App\Policies;

use App\Models\BankCard;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankCardPolicy
{
    use HandlesAuthorization;

    public function update(User $currentUser, BankCard $card)
    {
        return $currentUser->id === $card->user_id;
    }

    public function destroy(User $currentUser, BankCard $card)
    {
        return $currentUser->id === $card->user_id;
    }
}
