<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Exception;

use App\NotificationPublisher\Domain\Exception\TransportException as BaseTransportException;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * NOTE: In production code I would use separate exceptions for each transport as there are things to be marked as
 * #[\SensitiveParameter]. For the exercise I let myself be a bit briefer and use one for all.
 */

class TransportException extends BaseTransportException
{
    public function __construct(ResponseInterface $response, ?Throwable $previous = null)
    {
        parent::__construct($response->getContent(), $response->getStatusCode(), $previous);
    }
}
