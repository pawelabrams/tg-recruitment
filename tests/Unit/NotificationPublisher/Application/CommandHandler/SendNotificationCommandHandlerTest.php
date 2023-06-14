<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\SendNotification;
use App\NotificationPublisher\Application\CommandHandler\SendNotificationCommandHandler;
use App\NotificationPublisher\Domain\Notification;
use App\NotificationPublisher\Domain\NotificationPublisher;
use App\NotificationPublisher\Domain\Recipient\RecipientInterface;
use App\SharedKernel\Domain\UserId;
use App\User\Domain\UserRepositoryInterface;
use App\User\Infrastructure\UserMock;
use App\User\Infrastructure\UserMockRepository;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\TestCase;

class SendNotificationCommandHandlerTest extends TestCase
{
    public function createCommandHandler(
        UserRepositoryInterface $userRepository = null,
        NotificationPublisher $notificationPublisher = null
    ): SendNotificationCommandHandler
    {
        if (null === $userRepository) {
            $userRepository = new UserMockRepository();
        }

        if (null === $notificationPublisher) {
            $notificationPublisher = $this->createMock(NotificationPublisher::class);
        }

        return new SendNotificationCommandHandler($userRepository, $notificationPublisher);
    }

    public function testDisallowEmptyTitles(): void
    {
        $publisher = $this->createMock(NotificationPublisher::class);
        $publisher->expects(self::never())->method('send');

        $handle = $this->createCommandHandler(notificationPublisher: $publisher);

        $command = new SendNotification($this->createUserId(), '', ['sms']);

        $this->expectException(\InvalidArgumentException::class);
        $handle($command);
    }

    public function testSendsNotificationToPublisher(): void
    {
        $userId = $this->createUserId();
        $user = new UserMock();

        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->expects(self::once())->method('find')
            ->with($userId)->willReturn($user);

        $publisher = $this->createMock(NotificationPublisher::class);
        $publisher->expects(self::once())->method('send')->with(
            new Callback(static fn (Notification $n) => $n->getSubject() === 'This title is alright.'),
            new Callback(static fn (RecipientInterface $r) => $r->getPhone() === $user->getPhone())
        );

        $handle = $this->createCommandHandler($repository, $publisher);

        $command = new SendNotification($userId, 'This title is alright.', ['sms']);

        $handle($command);
    }

    /**
     * TODO: use Faker-provided or other random UUID
     */
    private function createUserId(): UserId
    {
        return new UserId('3fa85f64-5717-4562-b3fc-2c963f66afa6');
    }
}
