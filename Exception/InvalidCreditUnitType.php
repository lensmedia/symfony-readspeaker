<?php

namespace Lens\Bundle\ReadspeakerBundle\Exception;

use Lens\Bundle\ReadspeakerBundle\Response\Credit;
use RuntimeException;
use Throwable;

class InvalidCreditUnitType extends RuntimeException
{
    public function __construct(string $unit, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Invalid credit unit specified "%s", available options are: '.implode(', ', Credit::UNIT_TYPES), $unit),
            $code,
            $previous
        );
    }
}
