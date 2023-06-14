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
use App\NotificationPublisher\Infrastructure\Recipient\PushyRecipientInterface;
use App\NotificationPublisher\Infrastructure\Transport\PushyTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

class PushyTransportTest extends TestCase
{
    public static function createTransport(): PushyTransport
    {
        return new PushyTransport(
            'token', new MockHttpClient()
        );
    }

    public function nonPushMessageProvider(): iterable
    {
        $recipient = $this->createMock(PushyRecipientInterface::class);
        $recipient->method('hasPushyToken')->willReturn(true);
        $recipient->method('getPushyToken')->willReturn('abcd_token');

        yield 'no channels' => [new Message(
            new Notification('Hi', []),
            $recipient
        )];

        yield 'wrong channels' => [new Message(
            new Notification('Hi', ['email', 'sms']),
            $recipient
        )];
    }

    /**
     * @dataProvider nonPushMessageProvider
     */
    public function testDisallowNonPushMessages(Message $message): void
    {
        $transport = self::createTransport();

        $this->expectException(WrongChannelException::class);
        $transport->send($message);
    }

    public function nonPushyRecipientProvider(): iterable
    {
        $recipient = $this->createMock(RecipientInterface::class);
        yield 'not an PushyRecipient' => [new Message(
            new Notification('Hi', ['push']),
            $recipient
        )];
    }

    /**
     * @dataProvider nonPushyRecipientProvider
     */
    public function testDisallowNonPushyRecipients(Message $message): void
    {
        $transport = self::createTransport();

        $this->expectException(WrongRecipientTypeException::class);
        $transport->send($message);
    }

    public function noPushyTokenRecipientProvider(): iterable
    {
        $recipient = $this->createMock(PushyRecipientInterface::class);
        $recipient->method('hasPushyToken')->willReturn(false);
        $recipient->method('getPushyToken')->willReturn(null);

        yield 'no phone recipient' => [new Message(
            new Notification('Hi', ['push']),
            $recipient
        )];

        $recipient->method('getPushyToken')->willReturn('');

        yield 'empty recipient' => [new Message(
            new Notification('Hi', ['push']),
            $recipient
        )];
    }

    /**
     * @dataProvider noPushyTokenRecipientProvider
     */
    public function testDisallowNoPushyTokenRecipients(Message $message): void
    {
        $transport = self::createTransport();

        $this->expectException(RecipientUnreachableException::class);
        $transport->send($message);
    }
}
