<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Recipient;

use App\NotificationPublisher\Domain\Recipient\Recipient;
use App\User\Domain\UserInterface;

class UserRecipient extends Recipient implements PushyRecipientInterface
{
    use PushyRecipientTrait;

    public static function fromUser(UserInterface $user): static
    {
        return (new static())
            ->email($user->getEmail())
            ->phone($user->getPhone())
            ->pushyToken($user->getPushyToken());
    }
}
