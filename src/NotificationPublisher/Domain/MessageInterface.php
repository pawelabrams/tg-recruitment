<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

interface MessageInterface
{
    public function getNotification(): Notification;

    public function getRecipient(): RecipientInterface;
}
