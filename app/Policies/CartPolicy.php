<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Auth\Access\Response;

class CartPolicy
{
    public function view(User $user, Cart $cart)
    {
        return $user->id === $cart->user_id;
    }

    public function create(User $user)
    {
        return $user->role === 'buyer';
    }

    public function update(User $user, Cart $cart)
    {
        return $user->id === $cart->user_id;
    }

    public function delete(User $user, Cart $cart)
    {
        return $user->id === $cart->user_id;
    }
}