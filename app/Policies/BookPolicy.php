<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Determine whether the user can view any books.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the book.
     */
    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create books.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pustakawan'], true);
    }

    /**
     * Determine whether the user can update the book.
     */
    public function update(User $user, Book $book): bool
    {
        return in_array($user->role, ['admin', 'pustakawan'], true);
    }

    /**
     * Determine whether the user can delete the book.
     */
    public function delete(User $user, Book $book): bool
    {
        return in_array($user->role, ['admin', 'pustakawan'], true);
    }

    /**
     * Determine whether the user can view/download the original locked digital file.
     */
    public function viewOriginal(User $user, Book $book): bool
    {
        return in_array($user->role, ['admin', 'pustakawan'], true);
    }
}
