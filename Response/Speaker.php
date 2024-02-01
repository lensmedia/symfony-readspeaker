<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadspeakerBundle\Response;

readonly class Speaker
{
    private function __construct(
        public string $language,
        public string $voice,
        public Genders $gender,
    ) {
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            $data['language'],
            $data['voice'],
            Genders::tryFrom($data['gender'])
        );
    }
}
