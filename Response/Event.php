<?php

// <?

// time_ms":"0",
// type":"SENT",
// text_start":"0",
// text_end":"19",
// text_length":"19"

namespace Lens\Bundle\ReadspeakerBundle\Response;

use Lens\Bundle\ReadspeakerBundle\Exception\InvalidEventType;

class Event
{
    const EVENT_TEXTSTART = 'TEXTSTART';
    const EVENT_SENT = 'SENT';
    const EVENT_WORD = 'WORD';
    const EVENT_TEXTEND = 'TEXTEND';

    const EVENT_TYPES = [
        self::EVENT_TEXTSTART,
        self::EVENT_SENT,
        self::EVENT_WORD,
        self::EVENT_TEXTEND,
    ];

    /** @var int Time in miliseconds. */
    private $time;
    private $type;
    private $start;
    private $end;
    private $length;

    public static function fromApiResponse(array $data)
    {
        $instance = (new self())
            ->setTime((int) $data['time_ms'])
            ->setType($data['type']);

        if (isset($data['text_start'])) {
            $instance->setStart((int) $data['text_start']);
        }

        if (isset($data['text_end'])) {
            $instance->setEnd((int) $data['text_end']);
        }

        if (isset($data['text_length'])) {
            $instance->setLength((int) $data['text_length']);
        }

        return $instance;
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

    private function setType(string $type): self
    {
        if (!in_array($type, self::EVENT_TYPES)) {
            throw new InvalidEventType($type);
        }

        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    private function setStart(int $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    private function setEnd(int $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    private function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }
}
