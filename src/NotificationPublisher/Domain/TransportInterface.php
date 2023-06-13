<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

interface TransportInterface
{
    public function send(MessageInterface $message): bool;

    public function supports(MessageInterface $message): bool;
}
