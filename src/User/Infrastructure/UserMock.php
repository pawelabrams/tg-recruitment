<?php

declare(strict_types=1);

namespace App\User\Infrastructure;

use App\User\Domain\UserInterface;

class UserMock implements UserInterface
{
    public function getEmail(): ?string
    {
        return getenv('MOCK_USER_EMAIL') ?: 'user@user.example';
    }

    public function getPhone(): ?string
    {
        return getenv('MOCK_USER_PHONE') ?: '';
    }

    public function getPushyToken(): ?string
    {
        return getenv('MOCK_USER_PUSHY_TOKEN') ?: '';
    }
}
