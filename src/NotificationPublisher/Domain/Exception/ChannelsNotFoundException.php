<?php

namespace App\NotificationPublisher\Domain\Exception;

use Throwable;

class ChannelsNotFoundException extends \Exception
{
    public function __construct(array $channelNames, ?Throwable $previous = null)
    {
        $message = sprintf('Channel names weren\'t found in the configuration: "%s"', join('", "', $channelNames));

        parent::__construct($message, 0, $previous);
    }
}
