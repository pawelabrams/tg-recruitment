<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

use App\NotificationPublisher\Domain\Exception\TransportException;

interface TransportInterface
{
    /**
     * @throws TransportException
     */
    public function send(MessageInterface $message): bool;

    public function supports(MessageInterface $message): bool;
}
