<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Recipient;

trait SmsRecipientTrait
{
    private ?string $phone;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function hasPhone(): bool
    {
        return !empty($this->phone);
    }

    public function phone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
