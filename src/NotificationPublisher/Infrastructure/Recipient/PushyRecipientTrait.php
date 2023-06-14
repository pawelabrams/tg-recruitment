<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Recipient;

trait PushyRecipientTrait
{
    private ?string $pushyToken;

    public function hasPushyToken(): bool
    {
        return !empty($this->pushyToken);
    }

    public function getPushyToken(): ?string
    {
        return $this->pushyToken;
    }

    public function pushyToken(string $pushyToken): self
    {
        $this->pushyToken = $pushyToken;

        return $this;
    }
}
