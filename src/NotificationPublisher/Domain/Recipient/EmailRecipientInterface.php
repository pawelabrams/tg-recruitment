<?php
declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Recipient;

interface EmailRecipientInterface extends RecipientInterface
{
    public function hasEmail(): bool;
    public function getEmail(): ?string;
}
