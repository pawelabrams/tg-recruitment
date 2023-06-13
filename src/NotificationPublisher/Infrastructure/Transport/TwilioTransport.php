<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Transport;

use App\NotificationPublisher\Domain\MessageInterface;
use App\NotificationPublisher\Domain\TransportInterface;

class TwilioTransport implements TransportInterface
{
    public function send(MessageInterface $message): bool
    {
        // TODO: Implement send() method.
    }

    public function supports(MessageInterface $message): bool
    {
        return in_array('sms', $message->getNotification()->getChannels());
            // && $message->getRecipient() instanceof SmsRecipientInterface; //TODO
    }
}
