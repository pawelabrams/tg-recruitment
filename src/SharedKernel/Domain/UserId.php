<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

class UserId
{
    public function __construct(
        private readonly string $uuid
    )
    {
    }

    public function __toString() {
        return $this->uuid;
    }
}
