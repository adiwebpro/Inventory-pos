<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    public function view(User $user, Transaction $transaction)
    {
        return $user->id === $transaction->user_id || 
               in_array($user->role, ['admin', 'cashier', 'owner']);
    }

    public function create(User $user)
    {
        return $user->role === 'buyer';
    }

    public function update(User $user, Transaction $transaction)
    {
        return in_array($user->role, ['admin', 'cashier']);
    }

    public function delete(User $user, Transaction $transaction)
    {
        return $user->role === 'admin';
    }
}