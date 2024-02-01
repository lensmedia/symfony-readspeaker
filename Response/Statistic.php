<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadspeakerBundle\Response;

use DateTimeImmutable;
use DateTimeInterface;

readonly class Statistic
{
    private function __construct(
        public int $app,
        public string $language,
        public string $voice,
        public DateTimeInterface $date,
        public int $calls,
        public int $text,
        public int $time,
    ) {
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            $data['app'],
            $data['language'],
            $data['voice'],
            new DateTimeImmutable($data['date']),
            $data['calls'],
            $data['text'],
            $data['time'],
        );
    }
}
