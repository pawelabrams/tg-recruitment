<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

use App\SharedKernel\Domain\UserId;

class SendNotification
{
    public function __construct(
        public readonly UserId  $userId,
        public readonly string  $title,
        public readonly array   $channels = [],
        public readonly ?string $content = null,
    )
    {
    }
}
