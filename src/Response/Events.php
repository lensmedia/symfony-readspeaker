<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadSpeakerBundle\Response;

enum Events: string
{
    case TextStart = 'TEXTSTART';
    case Sent = 'SENT';
    case Word = 'WORD';
    case TextEnd = 'TEXTEND';
}
