<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Exception;

use App\NotificationPublisher\Domain\Exception\TransportException as BaseTransportException;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class RecipientUnreachableException extends BaseTransportException
{
}
