<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\SendNotification;
use App\NotificationPublisher\Domain\Notification;
use App\NotificationPublisher\Domain\NotificationPublisher;
use App\NotificationPublisher\Infrastructure\Recipient\UserRecipient;
use App\User\Domain\UserRepositoryInterface;

class SendNotificationCommandHandler
{
    protected const ALL_CHANNELS = ['email', 'sms', 'push'];

    public function __construct(
        private UserRepositoryInterface $repository,
        private NotificationPublisher $notificationPublisher
    )
    {
    }

    public function __invoke(SendNotification $command): void
    {
        if (empty($command->title)) {
            throw new \InvalidArgumentException('Title must not be empty');
        }

        $user = $this->repository->find($command->userId);
        $recipient = UserRecipient::fromUser($user);

        $notification = new Notification(
            $command->title,
            $command->channels ?: self::ALL_CHANNELS
        );

        if (null !== $command->content) {
            $notification->setContent($command->content);
        }

        $this->notificationPublisher->send($notification, $recipient);
    }
}
