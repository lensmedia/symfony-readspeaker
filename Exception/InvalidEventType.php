<?php

namespace Lens\Bundle\ReadspeakerBundle\Exception;

use Lens\Bundle\ReadspeakerBundle\Response\Event;
use RuntimeException;
use Throwable;

class InvalidEventType extends RuntimeException
{
    public function __construct(string $event, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Invalid event specified "%s", available options are: '.implode(', ', Event::EVENT_TYPES), $event),
            $code,
            $previous
        );
    }
}
