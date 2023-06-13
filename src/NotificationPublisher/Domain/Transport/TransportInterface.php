<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Transport;

use App\NotificationPublisher\Domain\Exception\TransportException;
use App\NotificationPublisher\Domain\Message\MessageInterface;

interface TransportInterface
{
    /**
     * @throws TransportException
     */
    public function send(MessageInterface $message): bool;

    public function supports(MessageInterface $message): bool;
}
