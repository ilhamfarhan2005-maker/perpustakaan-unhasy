<?php

namespace App\Policies;

use App\Models\FinePayment;
use App\Models\User;

class FinePaymentPolicy
{
    /**
     * Determine whether the user can view any fine payments.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pustakawan'], true);
    }

    /**
     * Determine whether the user can view the fine payment.
     */
    public function view(User $user, FinePayment $payment): bool
    {
        if (in_array($user->role, ['admin', 'pustakawan'], true)) {
            return true;
        }

        return $user->isMahasiswa() && (int) $payment->user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can create fine payments.
     */
    public function create(User $user): bool
    {
        return $user->isMahasiswa();
    }

    /**
     * Determine whether the user can verify fine payments.
     */
    public function verify(User $user, FinePayment $payment): bool
    {
        return in_array($user->role, ['admin', 'pustakawan'], true);
    }

    /**
     * Determine whether the user can update fine payments.
     */
    public function update(User $user, FinePayment $payment): bool
    {
        return in_array($user->role, ['admin', 'pustakawan'], true);
    }

    /**
     * Determine whether the user can delete fine payments.
     */
    public function delete(User $user, FinePayment $payment): bool
    {
        return false;
    }
}
