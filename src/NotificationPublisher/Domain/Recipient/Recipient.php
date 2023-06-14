<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Recipient;

class Recipient implements SmsRecipientInterface, EmailRecipientInterface
{
    use SmsRecipientTrait;
    use EmailRecipientTrait;
}
