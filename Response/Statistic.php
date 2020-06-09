<?php

namespace Lens\Bundle\ReadspeakerBundle\Response;

use DateTimeImmutable;
use DateTimeInterface;

class Statistic
{
    private $app;
    private $language;
    private $voice;
    private $date;
    private $calls;
    private $text;
    private $time;

    public static function fromApiResponse(array $data)
    {
        return (new self())
            ->setApp($data['app'])
            ->setLanguage($data['language'])
            ->setVoice($data['voice'])
            ->setDate(new DateTimeImmutable($data['date']))
            ->setCalls($data['calls'])
            ->setText($data['text'])
            ->setTime($data['time']);
    }

    private function setApp(int $app): self
    {
        $this->app = $app;

        return $this;
    }

    public function getApp(): int
    {
        return $this->app;
    }

    private function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    private function setVoice(string $voice): self
    {
        $this->voice = $voice;

        return $this;
    }

    public function getVoice(): string
    {
        return $this->voice;
    }

    private function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    private function setCalls(int $calls): self
    {
        $this->calls = $calls;

        return $this;
    }

    public function getCalls(): int
    {
        return $this->calls;
    }

    private function setText(int $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): int
    {
        return $this->text;
    }

    private function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }
}
