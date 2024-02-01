<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadspeakerBundle\Response;

readonly class Event
{
    private function __construct(
        /** @var int Time in milliseconds. */
        public int $time,
        public Events $type,
        public int $start,
        public int $end,
        public int $length,
    ) {
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            (int)($data['time_ms'] ?? 0),
            Events::tryFrom($data['type']),
            (int)($data['text_start'] ?? 0),
            (int)($data['text_end'] ?? 0),
            (int)($data['text_length'] ?? 0),
        );
    }
}
