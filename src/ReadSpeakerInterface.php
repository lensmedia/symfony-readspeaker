<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadSpeakerBundle;

interface ReadSpeakerInterface
{
    public function produce(string $text, bool $ssml = false);

    public function statistics();

    public function voiceInfo();

    public function credits();

    public function events(string $text, bool $ssml = false);
}
