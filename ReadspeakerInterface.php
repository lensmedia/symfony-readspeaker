<?php

namespace Lens\Bundle\ReadspeakerBundle;

interface ReadspeakerInterface
{
    public function produce(string $text, bool $ssml = false);

    public function statistics();

    public function voiceInfo();

    public function credits();

    public function events(string $text, bool $ssml = false);
}
