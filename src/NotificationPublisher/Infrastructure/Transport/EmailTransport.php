<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Transport;

use App\NotificationPublisher\Domain\Message\MessageInterface;
use App\NotificationPublisher\Domain\Transport\TransportInterface;

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
