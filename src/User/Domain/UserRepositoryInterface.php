<?php

declare(strict_types=1);

namespace App\User\Domain;

use App\SharedKernel\Domain\UserId;

interface UserRepositoryInterface
{
    /**
     * TODO: should throw UserNotFoundException
     */
    public function find(UserId $userId): UserInterface;
}
