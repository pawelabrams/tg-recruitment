<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Exception;

use App\NotificationPublisher\Domain\Exception\TransportException as BaseTransportException;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * NOTE: I would love for this class to get __CLASS__ as an only param, or maybe __FILE__ and __LINE__ as well.
 */
class WrongRecipientTypeException extends BaseTransportException
{
}
