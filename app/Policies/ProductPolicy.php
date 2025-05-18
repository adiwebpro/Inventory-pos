<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Product $product)
    {
        return true;
    }

    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'stock_counter']);
    }

    public function update(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'stock_counter']);
    }

    public function delete(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'stock_counter']);
    }
}