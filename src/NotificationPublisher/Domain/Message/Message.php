<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Message;

use App\NotificationPublisher\Domain\Notification;
use App\NotificationPublisher\Domain\Recipient\RecipientInterface;

class Message implements MessageInterface
{
    public function __construct(
        private readonly Notification $notification,
        private readonly RecipientInterface $recipient
    )
    {
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }

    public function getRecipient(): RecipientInterface
    {
        return $this->recipient;
    }
}
