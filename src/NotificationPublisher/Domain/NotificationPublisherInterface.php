<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

use App\NotificationPublisher\Domain\Recipient\RecipientInterface;

/**
 * Based on Symfony Notifier component by Fabien Potencier <fabien@symfony.com>
 */
interface NotificationPublisherInterface
{
    public function send(Notification $notification, RecipientInterface ...$recipients): void;
}
