<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

class Notification
{
    public string $content = '';

    /**
     * @param list<string> $channels
     */
    public function __construct(public string $subject, public array $channels)
    {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
