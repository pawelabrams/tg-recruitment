<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

class Notification
{
    private string $content = '';

    /**
     * @param list<string> $channels
     */
    public function __construct(private readonly string $subject, private readonly array $channels)
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
