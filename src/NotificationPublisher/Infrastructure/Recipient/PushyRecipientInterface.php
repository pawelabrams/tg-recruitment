<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Recipient;

use App\NotificationPublisher\Domain\Recipient\RecipientInterface;

interface PushyRecipientInterface extends RecipientInterface
{
    public function hasPushyToken(): bool;
    public function getPushyToken(): ?string;
}
