<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Message;

use App\NotificationPublisher\Domain\Notification;
use App\NotificationPublisher\Domain\Recipient\RecipientInterface;

interface MessageInterface
{
    public function getNotification(): Notification;

    public function getRecipient(): RecipientInterface;
}
