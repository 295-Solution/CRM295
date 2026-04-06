<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin() || $quotation->lead?->assigned_to === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin() || $quotation->lead?->assigned_to === $user->id;
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        return $user->isAdmin() || $quotation->lead?->assigned_to === $user->id;
    }
}
