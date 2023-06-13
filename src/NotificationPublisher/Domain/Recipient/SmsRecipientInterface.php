<?php
declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Recipient;

interface SmsRecipientInterface extends RecipientInterface
{
    public function hasPhone(): bool;
    public function getPhone(): ?string;
}
