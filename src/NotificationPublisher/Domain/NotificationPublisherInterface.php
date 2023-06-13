<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

/**
 * Based on Symfony Notifier component by Fabien Potencier <fabien@symfony.com>
 */
interface NotificationPublisherInterface
{
    public function send(Notification $notification, RecipientInterface ...$recipients): void;
}
