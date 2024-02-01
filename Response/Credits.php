<?php

declare(strict_types=1);

namespace Lens\Bundle\ReadspeakerBundle\Response;

readonly class Credits
{
    private function __construct(
        public int $amount = 0,
        public CreditUnitTypes $unit = CreditUnitTypes::FlatRate,
    ) {
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            (int)($data['amount'] ?? 0),
            CreditUnitTypes::tryFrom($data['unit']),
        );
    }

    public function __toString()
    {
        if (CreditUnitTypes::FlatRate === $this->unit) {
            return $this->unit->value;
        }

        return $this->amount.' '.$this->unit->value;
    }
}
