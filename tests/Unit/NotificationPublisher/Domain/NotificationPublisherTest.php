<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain;

use App\NotificationPublisher\Domain\Message\Message;
use App\NotificationPublisher\Domain\Message\MessageInterface;
use App\NotificationPublisher\Domain\Notification;
use App\NotificationPublisher\Domain\NotificationPublisher;
use App\NotificationPublisher\Domain\Recipient\RecipientInterface;
use App\NotificationPublisher\Domain\Transport\TransportInterface;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\TestCase;

class NotificationPublisherTest extends TestCase
{
    private TransportInterface $pushTransport1;
    private TransportInterface $pushTransport2;
    private TransportInterface $smsTransport;
    private NotificationPublisher $publisher;

    protected function setUp(): void
    {
        $this->pushTransport1 = $this->createMock(TransportInterface::class);
        $this->pushTransport2 = $this->createMock(TransportInterface::class);
        $this->smsTransport = $this->createMock(TransportInterface::class);

        $this->publisher = new NotificationPublisher([
            'push' => [$this->pushTransport1, $this->pushTransport2],
            'sms' => [$this->smsTransport],
        ]);
    }


    public function testFailoverStrategy(): void
    {
        $recipientWithPhoneAndPushOne = $this->createMock(RecipientInterface::class);
        $recipientWithPhoneAndPushTwo = $this->createMock(RecipientInterface::class);
        $recipientWithOnlyPushOne = $this->createMock(RecipientInterface::class);
        $recipientWithOnlyPushTwo = $this->createMock(RecipientInterface::class);
        $recipientWithOnlyPhone = $this->createMock(RecipientInterface::class);
        $recipientWithPhoneAndBothPush = $this->createMock(RecipientInterface::class);

        $this->pushTransport1->method('supports')
            ->willReturnCallback(static fn (MessageInterface $message) => in_array(
                $message->getRecipient(),
                [$recipientWithPhoneAndPushOne, $recipientWithOnlyPushOne, $recipientWithPhoneAndBothPush],
                true
            ));

        $this->pushTransport2->method('supports')
            ->willReturnCallback(static fn (MessageInterface $message) => in_array(
                $message->getRecipient(),
                [$recipientWithPhoneAndPushTwo, $recipientWithOnlyPushTwo, $recipientWithPhoneAndBothPush],
                true
            ));

        $this->smsTransport->method('supports')
            ->willReturnCallback(static fn (MessageInterface $message) => in_array(
                $message->getRecipient(),
                [$recipientWithPhoneAndPushOne, $recipientWithPhoneAndPushTwo, $recipientWithOnlyPhone, $recipientWithPhoneAndBothPush],
                true
            ));

        $notification = new Notification('subject', ['push', 'sms']);
        $this->pushTransport1->expects($this->exactly(3))->method('send')
            ->withConsecutive(
                [$this->checkRecipient($recipientWithPhoneAndPushOne)],
                [$this->checkRecipient($recipientWithOnlyPushOne)],
                [$this->checkRecipient($recipientWithPhoneAndBothPush)],
            );
        $this->pushTransport2->expects($this->exactly(2))->method('send')
            ->withConsecutive(
                [$this->checkRecipient($recipientWithPhoneAndPushTwo)],
                [$this->checkRecipient($recipientWithOnlyPushTwo)],
                // NOTE: no $recipientWithPhoneAndBothPush, as this recipient was already notified!
            );
        $this->smsTransport->expects($this->exactly(4))->method('send')
            ->withConsecutive(
                [$this->checkRecipient($recipientWithPhoneAndPushOne)],
                [$this->checkRecipient($recipientWithPhoneAndPushTwo)],
                [$this->checkRecipient($recipientWithOnlyPhone)],
                [$this->checkRecipient($recipientWithPhoneAndBothPush)], // NOTE: notified again as they have both channels configured
            );

        $this->publisher->send(
            $notification,
            $recipientWithPhoneAndPushOne,
            $recipientWithPhoneAndPushTwo,
            $recipientWithOnlyPushOne,
            $recipientWithOnlyPushTwo,
            $recipientWithOnlyPhone,
            $recipientWithPhoneAndBothPush
        );
    }

    private function checkRecipient(RecipientInterface $recipient): Callback
    {
        return new Callback(static fn(Message $message): bool => $message->getRecipient() === $recipient);
    }
}
