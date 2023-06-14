<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Transport;

use App\NotificationPublisher\Domain\Message\MessageInterface;
use App\NotificationPublisher\Domain\Transport\TransportInterface;
use App\NotificationPublisher\Infrastructure\Exception\RecipientUnreachableException;
use App\NotificationPublisher\Infrastructure\Exception\WrongChannelException;
use App\NotificationPublisher\Infrastructure\Exception\WrongRecipientTypeException;
use App\NotificationPublisher\Infrastructure\Recipient\PushyRecipientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PushyTransport implements TransportInterface
{
    protected const HOST = 'api.pushy.me';

    private readonly string $secretApiKey;
    private HttpClientInterface $client;

    public function __construct(
        #[\SensitiveParameter] string $secretApiKey,
        HttpClientInterface           $client = null
    )
    {
        $this->secretApiKey = $secretApiKey;
        $this->client = $client;

        if (null === $client) {
            $this->client = HttpClient::create();
        }
    }

    public function send(MessageInterface $message): bool
    {
        if (!in_array('push', $message->getNotification()->getChannels())) {
            throw new WrongChannelException();
        }

        $recipient = $message->getRecipient();

        if (!$recipient instanceof PushyRecipientInterface) {
            throw new WrongRecipientTypeException();
        }

        if (!$recipient->hasPushyToken()) {
            throw new RecipientUnreachableException('No phone provided.');
        }

        $notification = $message->getNotification();

        $endpoint = sprintf('https://%s/push?api_key=%s', $this->getEndpoint(), $this->secretApiKey);
        $body = [
            'to' => $recipient->getPushyToken(),
            'notification' => [
                'title' => $notification->getSubject(),
                'body' => $notification->getContent(),
            ],
        ];
        $response = $this->client->request('POST', $endpoint, [
            'body' => $body,
        ]);

        return $response->getStatusCode() < 400;
    }

    public function supports(MessageInterface $message): bool
    {
        $recipient = $message->getRecipient();

        return in_array('push', $message->getNotification()->getChannels())
            && $recipient instanceof PushyRecipientInterface
            && $recipient->hasPushyToken();
    }

    private function getEndpoint(): string
    {
        return self::HOST;
    }
}
