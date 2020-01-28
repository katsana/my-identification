<?php

namespace Malaysia\Identification;

use Serializable;

class PlateNumber implements Serializable
{
    /**
     * Plate number regular expression pattern.
     */
    public const REGEX = '/^([A-Za-z]+|A1M|G1M|K1M|T1M|1M4U)\s?([0-9]{1,4})\s?([A-Za-z]{1,2})?$/';

    /**
     * Plate number value prefix.
     *
     * @var string|null
     */
    protected $platePrefix;

    /**
     * Plate number value.
     *
     * @var int|null
     */
    protected $plateNumber;

    /**
     * Plate number value suffix.
     *
     * @var string|null
     */
    protected $plateSuffix;

    /**
     * Construct a new plate number value object.
     */
    public function __construct(string $plateNumber)
    {
        if (! \preg_match(self::REGEX, $plateNumber, $matches)) {
            return;
        }

        $this->platePrefix = \strtoupper($matches[1]);
        $this->plateNumber = $matches[2];
        $this->plateSuffix = isset($matches[3]) ? \strtoupper($matches[3]) : null;
    }

    /**
     * Construct a new Plate Number value object.
     *
     * @return static
     */
    public static function given(string $plateNumber)
    {
        return new static($plateNumber);
    }

    /**
     * Validate if current NRIC is valid.
     */
    public function isValid(): bool
    {
        return ! \is_null($this->platePrefix) && ! \is_null($this->plateNumber);
    }

    /**
     * Plate number prefix.
     */
    public function prefix(): ?string
    {
        return $this->platePrefix;
    }

    /**
     * Plate number number.
     */
    public function number(): ?int
    {
        return $this->plateNumber;
    }

    /**
     * Plate number suffix.
     */
    public function suffix(): ?string
    {
        return $this->plateSuffix;
    }

    /**
     * Convert to an array.
     */
    public function toArray(): array
    {
        return $this->isValid() ? \array_filter([
            $this->platePrefix,
            $this->plateNumber,
            $this->plateSuffix,
        ]) : [];
    }

    /**
     * Format Plate Number as string.
     */
    public function format(string $separator = ''): string
    {
        return $this->isValid() ? \implode($separator, $this->toArray()) : '';
    }

    /**
     * Convert Plate Number to formatted string format.
     */
    public function toFormattedString(): string
    {
        return $this->format(' ');
    }

    /**
     * Convert Plate Number to standard string format.
     */
    public function toStandardString(): string
    {
        return $this->format();
    }

    /**
     * Convert Plate Number to string.
     */
    public function __toString(): string
    {
        return $this->toStandardString();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $data = $this->toArray();

        return \serialize([
            'prefix' => $data[0] ?? null,
            'number' => $data[1] ?? null,
            'suffix' => $data[2] ?? null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        [
            'prefix' => $this->platePrefix,
            'number' => $this->plateNumber,
            'suffix' => $this->plateSuffix,
        ] = \unserialize($data);
    }
}
