<?php

namespace Lens\Bundle\ReadspeakerBundle\Response;

use Lens\Bundle\ReadspeakerBundle\Exception\InvalidCreditUnitType;

class Credits
{
    const UNIT_SECONDS = 'seconds';
    const UNIT_CHARACTERS = 'characters';
    const UNIT_FLAT_RATE = 'flat rate';

    const UNIT_TYPES = [
        self::UNIT_SECONDS,
        self::UNIT_CHARACTERS,
        self::UNIT_FLAT_RATE,
    ];

    private $amount;
    private $unit;

    public static function fromApiResponse(array $data)
    {
        return (new self())
            ->setAmount($data['amount'])
            ->setUnit($data['unit']);
    }

    private function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    private function setUnit(string $unit): self
    {
        if (!in_array($unit, self::UNIT_TYPES)) {
            throw new InvalidCreditUnitType($unit);
        }

        $this->unit = $unit;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function __toString()
    {
        if (self::UNIT_FLAT_RATE === $this->unit) {
            return $this->unit;
        }

        return $this->amount.' '.$this->unit;
    }
}
