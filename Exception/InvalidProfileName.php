<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadspeakerBundle\Exception;

use RuntimeException;
use Throwable;

class InvalidProfileName extends RuntimeException
{
    public function __construct(string $profile, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Invalid configuration profile provided "%s".', $profile),
            $code,
            $previous
        );
    }
}
