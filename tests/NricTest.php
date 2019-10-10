<?php

namespace Malaysia\Identification\Tests;

use Carbon\CarbonInterface;
use Malaysia\Identification\Nric;
use PHPUnit\Framework\TestCase;

class NricTest extends TestCase
{
    /**
     * @test
     * @dataProvider validNricDataProvider
     */
    public function it_can_initiate_valid_identification_numbers($given, $formatted, $placeOfBirthCode, $genderCode, $asArray)
    {
        $nric = Nric::given($given);

        $this->assertTrue($nric->isValid());
        $this->assertSame($formatted, $nric->toFormattedString());
        $this->assertSame($given, (string) $nric);
        $this->assertInstanceOf(CarbonInterface::class, $nric->birthDate());
        $this->assertSame($placeOfBirthCode, $nric->placeOfBirthCode());
        $this->assertSame($genderCode, $nric->genderCode());
        $this->assertSame($asArray, $nric->toArray());
    }

    /**
     * @test
     * @dataProvider invalidNricDataProvider
     */
    public function it_can_initiate_invalid_identification_numbers($given, $formatted)
    {
        $nric = Nric::given($given);

        $this->assertFalse($nric->isValid());
        $this->assertSame('', $nric->toFormattedString());
        $this->assertSame('', (string) $nric);
        $this->assertNull($nric->birthDate());
        $this->assertNull($nric->placeOfBirthCode());
        $this->assertNull($nric->genderCode());
        $this->assertSame([], $nric->toArray());
    }

    /**
     * Valid NRIC data provider.
     *
     * @return array
     */
    public function validNricDataProvider()
    {
        $nrics = [
            ['810102', '08', '1110'],
            ['811231', '08', '1110'],
            ['120228', '08', '1110'], // Leap year
        ];

        $data = [];

        foreach ($nrics as $nric) {
            $data[] = [
                \implode('', $nric),
                \implode('-', $nric),
                $nric[1],
                $nric[2],
                $nric
            ];
        }

        return $data;
    }

    /**
     * Invalid NRIC data provider.
     *
     * @return array
     */
    public function invalidNricDataProvider()
    {
        $nrics = [
            ['811402', '08', '1110'],
            ['811231', 'aa', '1110'],
            ['120228', '08', 'aaaa'],
        ];

        $data = [];

        foreach ($nrics as $nric) {
            $data[] = [
                \implode('', $nric),
                \implode('-', $nric),
            ];
        }

        return $data;
    }
}
