<?php

declare(strict_types=1);

namespace App\User\Infrastructure;

use App\SharedKernel\Domain\UserId;
use App\User\Domain\UserInterface;
use App\User\Domain\UserRepositoryInterface;

class UserMockRepository implements UserRepositoryInterface
{
    private UserMock $mock;

    public function __construct()
    {
        $this->mock = new UserMock();
    }

    public function find(UserId $userId): UserInterface
    {
        return $this->mock;
    }
}
