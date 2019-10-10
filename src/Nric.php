<?php

namespace Nric;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeInterface;

class Nric
{
    protected $birthDate;
    protected $placeOfBirthCode;
    protected $genderCode;

    public function __construct(string $nricNumber)
    {
        if (! \preg_match('/^(\d{2}[0-1][0-9][0-3][0-9])-?(\d{2})-?(\d{4})/', $nricNumber, $matches)) {
            throw new \InvalidArgumentException('Not a valid NRIC number');
        }

        $this->birthDate = $this->formatBirthDate($matches[1]);
        $this->placeOfBirthCode = $matches[2];
        $this->genderCode = $matches[3];
    }

    public static function given(string $nricNumber)
    {
        return new static($nricNumber);
    }

    public function isValid(): bool
    {
        return $this->birthDate instanceof CarbonInterface
            && ! empty($this->placeOfBirthCode)
            && ! empty($this->genderCode);
    }

    public function birthDate(): ?CarbonInterface
    {
        return $this->birthDate;
    }

    public function placeOfBirthCode(): ?string
    {
        return $this->placeOfBirthCode;
    }

    public function genderCode(): ?string
    {
        return $this->genderCode;
    }

    public function toArray(): array
    {
        return [
            $this->birthDate->format('ymd'),
            $this->placeOfBirthCode,
            $this->genderCode,
        ];
    }

    public function format(string $separator = ''): string
    {
        return $this->isValid() ? \implode($separator, $this->toArray()) : '';
    }

    public function toFormattedString(): string
    {
        return $this->format('-');
    }

    public function toStandardString(): string
    {
        return $this->format();
    }

    public function __toString(): string
    {
        return $this->toStandardString();
    }

    protected function formatBirthDate(string $birthDate): ?CarbonInterface
    {
        $year = (int) \substr($birthDate, 0, 2);
        $now = CarbonImmutable::now()->format('y');

        $date = CarbonImmutable::createFromDate(
            ($year < $now ? '20' : '19').\str_pad($year, 2, '0', STR_PAD_LEFT),
            \substr($birthDate, 2, 2),
            \substr($birthDate, 4, 2)
        );

        return $birthDate === $date->format('ymd') ? $date : null;
    }
}
