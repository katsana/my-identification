<?php

namespace Malaysia\Identification;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Serializable;

class NRIC implements Serializable
{
    /**
     * Birthdate.
     *
     * @var \Carbon\CarbonInterface|null
     */
    protected $birthDate;

    /**
     * Place of birth code.
     *
     * @var string|null
     */
    protected $placeOfBirthCode;

    /**
     * Gender code.
     *
     * @var string|null
     */
    protected $genderCode;

    /**
     * Construct a new NRIC value object.
     */
    public function __construct(string $nricNumber)
    {
        if (! \preg_match('/^(\d{2}[0-1][0-9][0-3][0-9])-?(\d{2})-?(\d{4})/', $nricNumber, $matches)) {
            return;
        }

        $this->birthDate = $this->formatBirthDate($matches[1]);

        if ($this->birthDate instanceof CarbonInterface) {
            $this->placeOfBirthCode = $matches[2];
            $this->genderCode = $matches[3];
        }
    }

    /**
     * Construct a new NRIC value object.
     *
     * @return static
     */
    public static function given(string $nricNumber)
    {
        return new static($nricNumber);
    }

    /**
     * Validate if current NRIC is valid.
     */
    public function isValid(): bool
    {
        return $this->birthDate instanceof CarbonInterface
            && ! empty($this->placeOfBirthCode)
            && ! empty($this->genderCode);
    }

    /**
     * Get birthdate value.
     */
    public function birthDate(): ?CarbonInterface
    {
        return $this->birthDate;
    }

    /**
     * Get place of birthdate code value.
     */
    public function placeOfBirthCode(): ?string
    {
        return $this->placeOfBirthCode;
    }

    /**
     * Get gender code value.
     */
    public function genderCode(): ?string
    {
        return $this->genderCode;
    }

    /**
     * Convert to an array.
     */
    public function toArray(): array
    {
        return $this->isValid() ? [
            $this->birthDate->format('ymd'),
            $this->placeOfBirthCode,
            $this->genderCode,
        ] : [];
    }

    /**
     * Format NRIC as string.
     */
    public function format(string $separator = ''): string
    {
        return $this->isValid() ? \implode($separator, $this->toArray()) : '';
    }

    /**
     * Convert NRIC to formatted string format.
     */
    public function toFormattedString(): string
    {
        return $this->format('-');
    }

    /**
     * Convert NRIC to standard string format.
     */
    public function toStandardString(): string
    {
        return $this->format();
    }

    /**
     * Convert NRIC to string.
     */
    public function __toString(): string
    {
        return $this->toStandardString();
    }

    /**
     * Format birth date from NRIC.
     */
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

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $data = $this->toArray();

        return \serialize([
            'birthDate' => $data[0] ?? null,
            'placeOfBirthCode' => $data[1] ?? null,
            'genderCode' => $data[2] ?? null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        [
            'birthDate' => $birthDate,
            'placeOfBirthCode' => $this->placeOfBirthCode,
            'genderCode' => $this->genderCode,
        ] = \unserialize($data);

        $this->birthDate = CarbonImmutable::createFromFormat('ymd', $birthDate);
    }
}
