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
use App\NotificationPublisher\Infrastructure\Transport\EmailTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class EmailTransportTest extends TestCase
{
    public function createTransport(MailerInterface $mailer = null): EmailTransport
    {
        if (!$mailer) {
            $mailer = $this->createMock(MailerInterface::class);
        }

        return new EmailTransport('TG <tg@tg.example>', $mailer);
    }

    public function nonEmailMessageProvider(): iterable
    {
        yield 'no channels' => [new Message(
            new Notification('Hi', []),
            (new Recipient())->email('hello@email.test')
        )];

        yield 'wrong channels' => [new Message(
            new Notification('Hi', ['sms', 'push']),
            (new Recipient())->email('hello@company.test')
        )];
    }

    /**
     * @dataProvider nonEmailMessageProvider
     */
    public function testDisallowNonEmailMessages(Message $message): void
    {
        $transport = $this->createTransport();

        $this->expectException(WrongChannelException::class);
        $transport->send($message);
    }

    public function nonEmailMessageRecipientProvider(): iterable
    {
        $recipient = $this->createMock(RecipientInterface::class);
        yield 'not an SmsRecipient' => [new Message(
            new Notification('Hi', ['email']),
            $recipient
        )];
    }

    /**
     * @dataProvider nonEmailMessageRecipientProvider
     */
    public function testDisallowNonEmailMessageRecipients(Message $message): void
    {
        $transport = $this->createTransport();

        $this->expectException(WrongRecipientTypeException::class);
        $transport->send($message);
    }

    public function noEmailProvidedRecipientProvider(): iterable
    {
        yield 'no phone recipient' => [new Message(
            new Notification('Hi', ['email']),
            new Recipient()
        )];
        yield 'empty recipient' => [new Message(
            new Notification('Hi', ['email']),
            (new Recipient())->email('')
        )];
    }

    /**
     * @dataProvider noEmailProvidedRecipientProvider
     */
    public function testDisallowNoEmailProvidedRecipients(Message $message): void
    {
        $transport = $this->createTransport();

        $this->expectException(RecipientUnreachableException::class);
        $transport->send($message);
    }
}
