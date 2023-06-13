<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Transport;

use App\NotificationPublisher\Domain\Message\MessageInterface;
use App\NotificationPublisher\Domain\Recipient\SmsRecipientInterface;
use App\NotificationPublisher\Domain\Transport\TransportInterface;
use App\NotificationPublisher\Infrastructure\Exception\RecipientUnreachableException;
use App\NotificationPublisher\Infrastructure\Exception\WrongChannelException;
use App\NotificationPublisher\Infrastructure\Exception\WrongRecipientTypeException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwilioTransport implements TransportInterface
{
    protected const HOST = 'api.twilio.com';
    private readonly string $authToken;
    private HttpClientInterface $client;

    public function __construct(
        private readonly string       $accountSid,
        #[\SensitiveParameter] string $authToken,
        private readonly string       $from,
        HttpClientInterface           $client = null
    )
    {
        $this->authToken = $authToken;
        $this->client = $client;

        if (null === $client) {
            $this->client = HttpClient::create();
        }
    }

    public function send(MessageInterface $message): bool
    {
        if (!in_array('sms', $message->getNotification()->getChannels())) {
            throw new WrongChannelException();
        }

        $recipient = $message->getRecipient();

        if (!$recipient instanceof SmsRecipientInterface) {
            throw new WrongRecipientTypeException();
        }

        if (!$recipient->hasPhone()) {
            throw new RecipientUnreachableException('No phone provided.');
        }

        // TODO: validation of the phone number

        $endpoint = sprintf('https://%s/2010-04-01/Accounts/%s/Messages.json', $this->getEndpoint(), $this->accountSid);
        $body = [
            'From' => $this->from,
            'To' => $recipient->getPhone(),
            'Body' => $message->getNotification()->getSubject(),
        ];

        $response = $this->client->request('POST', $endpoint, [
            'auth_basic' => [$this->accountSid, $this->authToken],
            'body' => $body,
        ]);
    }

    public function supports(MessageInterface $message): bool
    {
        $recipient = $message->getRecipient();

        return in_array('sms', $message->getNotification()->getChannels())
            && $recipient instanceof SmsRecipientInterface
            && $recipient->hasPhone();
    }

    private function getEndpoint(): string
    {
        return self::HOST;
    }
}
