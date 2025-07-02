<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadSpeakerBundle\Response;

enum CreditUnitTypes: string
{
    case Seconds = 'seconds';
    case Characters = 'characters';
    case FlatRate = 'flat rate';
}
