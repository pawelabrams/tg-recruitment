<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Infrastructure\Transport;

use App\NotificationPublisher\Domain\Message\Message;
use App\NotificationPublisher\Domain\Notification;
use App\NotificationPublisher\Domain\Recipient\Recipient;
use App\NotificationPublisher\Domain\Recipient\RecipientInterface;
use App\NotificationPublisher\Infrastructure\Exception\RecipientUnreachableException;
use App\NotificationPublisher\Infrastructure\Exception\WrongChannelException;
use App\NotificationPublisher\Infrastructure\Exception\WrongRecipientTypeException;
use App\NotificationPublisher\Infrastructure\Transport\TwilioTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

class TwilioTransportTest extends TestCase
{
    public static function createTransport(): TwilioTransport
    {
        return new TwilioTransport(
            'sid', 'token', 'TG', new MockHttpClient()
        );
    }

    public function nonSmsMessageProvider(): iterable
    {
        yield 'no channels' => [new Message(
            new Notification('Hi', []),
            (new Recipient())->phone('+48601601601')
        )];

        yield 'wrong channels' => [new Message(
            new Notification('Hi', ['email', 'push']),
            (new Recipient())->phone('+48601601601')
        )];
    }

    /**
     * @dataProvider nonSmsMessageProvider
     */
    public function testDisallowNonSmsMessages(Message $message): void
    {
        $transport = self::createTransport();

        $this->expectException(WrongChannelException::class);
        $transport->send($message);
    }

    public function nonSmsMessageRecipientProvider(): iterable
    {
        $recipient = $this->createMock(RecipientInterface::class);
        yield 'not an SmsRecipient' => [new Message(
            new Notification('Hi', ['sms']),
            $recipient
        )];
    }

    /**
     * @dataProvider nonSmsMessageRecipientProvider
     */
    public function testDisallowNonSmsMessageRecipients(Message $message): void
    {
        $transport = self::createTransport();

        $this->expectException(WrongRecipientTypeException::class);
        $transport->send($message);
    }

    public function noPhoneRecipientProvider(): iterable
    {
        yield 'no phone recipient' => [new Message(
            new Notification('Hi', ['sms']),
            new Recipient()
        )];
        yield 'empty recipient' => [new Message(
            new Notification('Hi', ['sms']),
            (new Recipient())->phone('')
        )];
    }

    /**
     * @dataProvider noPhoneRecipientProvider
     */
    public function testDisallowNoPhoneRecipients(Message $message): void
    {
        $transport = self::createTransport();

        $this->expectException(RecipientUnreachableException::class);
        $transport->send($message);
    }
}
