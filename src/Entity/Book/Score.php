<?php

namespace App\Entity\Book;

use Negotiation\Exception\InvalidArgument;

class Score
{
    public int $value = 0;

    public function __construct(int $value = 0)
    {
        $this->assertValueIsValid($value);
        $this->value = $value;
    }

    private function assertValueIsValid(int $value = 0): void
    {
        if ($value === 0) {
            return;
        }
        if ($value > 5 || $value < 0) {
            throw new InvalidArgument('El score tiene que estar entre 0 y 5');
        }
    }

    public static function create(int $value = 0): self
    {
        return new self($value);
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
}
