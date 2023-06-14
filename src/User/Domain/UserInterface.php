<?php

declare(strict_types=1);

namespace App\User\Domain;

interface UserInterface
{
    public function getPhone(): ?string;
    public function getEmail(): ?string;
    public function getPushyToken(): ?string;
}
