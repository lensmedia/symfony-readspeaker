<?php

namespace Lens\Bundle\ReadspeakerBundle\Exception;

use Lens\Bundle\ReadspeakerBundle\Response\Speaker;
use RuntimeException;
use Throwable;

class InvalidSpeakerGenderType extends RuntimeException
{
    public function __construct(string $unit, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Invalid speaker gender type "%s", available options are: '.implode(', ', Speaker::GENDER_TYPES), $unit),
            $code,
            $previous
        );
    }
}
