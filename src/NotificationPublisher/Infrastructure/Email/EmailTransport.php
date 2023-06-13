<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Email;

use App\NotificationPublisher\Domain\MessageInterface;
use App\NotificationPublisher\Domain\TransportInterface;

class EmailTransport implements TransportInterface
{
    public function send(MessageInterface $message): bool
    {
        // TODO: Implement send() method.
    }

    public function supports(MessageInterface $message): bool
    {
        return in_array('email', $message->getNotification()->getChannels());
    }
}
