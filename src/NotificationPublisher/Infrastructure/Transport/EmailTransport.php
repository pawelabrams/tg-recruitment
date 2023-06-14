<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Transport;

use App\NotificationPublisher\Domain\Message\MessageInterface;
use App\NotificationPublisher\Domain\Recipient\EmailRecipientInterface;
use App\NotificationPublisher\Domain\Transport\TransportInterface;
use App\NotificationPublisher\Infrastructure\Exception\RecipientUnreachableException;
use App\NotificationPublisher\Infrastructure\Exception\WrongChannelException;
use App\NotificationPublisher\Infrastructure\Exception\WrongRecipientTypeException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailTransport implements TransportInterface
{
    public function __construct(
        private readonly string          $from,
        private readonly MailerInterface $mailer
    )
    {
    }

    public function send(MessageInterface $message): bool
    {
        if (!in_array('email', $message->getNotification()->getChannels())) {
            throw new WrongChannelException();
        }

        $recipient = $message->getRecipient();

        if (!$recipient instanceof EmailRecipientInterface) {
            throw new WrongRecipientTypeException();
        }

        if (!$recipient->hasEmail()) {
            throw new RecipientUnreachableException('No e-mail address provided.');
        }

        $recipient = $message->getRecipient();
        $notification = $message->getNotification();

        $email = (new Email())
            ->from($this->from)
            ->to($recipient->getEmail())
            ->subject($notification->getSubject())
            ->text(strip_tags($notification->getContent()))
            ->html($notification->getContent());

        $this->mailer->send($email);
    }

    public function supports(MessageInterface $message): bool
    {
        $recipient = $message->getRecipient();

        return in_array('email', $message->getNotification()->getChannels())
            && $recipient instanceof EmailRecipientInterface
            && $recipient->hasPhone();
    }
}
