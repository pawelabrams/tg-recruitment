<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Recipient;

trait EmailRecipientTrait
{
    private ?string $email;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function hasEmail(): bool
    {
        return !empty($this->email);
    }

    public function email(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
