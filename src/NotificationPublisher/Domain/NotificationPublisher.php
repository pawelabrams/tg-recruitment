<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

use App\NotificationPublisher\Domain\Exception\ChannelsNotFoundException;
use App\NotificationPublisher\Domain\Exception\TransportException;
use App\NotificationPublisher\Domain\Message\Message;
use App\NotificationPublisher\Domain\Message\MessageInterface;
use App\NotificationPublisher\Domain\Recipient\RecipientInterface;
use App\NotificationPublisher\Domain\Transport\TransportInterface;

class NotificationPublisher implements NotificationPublisherInterface
{
    /**
     * @param array<string, list<TransportInterface>> $channels
     */
    public function __construct(public readonly array $channels)
    {
    }

    /**
     * @throws ChannelsNotFoundException
     */
    public function send(Notification $notification, RecipientInterface ...$recipients): void
    {
        $availableChannels = array_keys($this->channels);
        $channelsNotFound = array_filter(
            $notification->getChannels(),
            fn (string $channelName) => !in_array($channelName, $availableChannels)
        );

        if (!empty($channelsNotFound)) {
            throw new ChannelsNotFoundException($channelsNotFound);
        }

        // Note: sending to multiple channels will result in delivering to all channels the recipient can be reached by.
        // This could be mitigated with using a Strategy pattern here and using an appropriate strategy when another is needed.
        foreach ($notification->getChannels() as $channelName) {
            // Failover scheme: for each channel the recipients will try to be reached by the first transport,
            // the remaining will be tried by a second transport etc.
            $recipientsToBeNotified = $recipients;
            foreach ($this->channels[$channelName] as $transport) {
                $messages = array_map(
                    static fn (RecipientInterface $recipient): MessageInterface => new Message($notification, $recipient),
                    $recipientsToBeNotified
                );

                $reached = [];
                foreach ($messages as $message) {
                    if (!$transport->supports($message)) {
                        continue;
                    }

                    try {
                        $transport->send($message);

                        // If the message is sent successfully,
                        // remove the recipient from unreachable recipients list at the end of the transport iteration.
                        $reached[] = $message->getRecipient();
                    } catch (TransportException) {
                        // TODO: log the sending failure but don't interrupt the sending for now
                    }
                }

                $recipientsToBeNotified = array_diff($recipientsToBeNotified, $reached);
            }
        }
    }
}
