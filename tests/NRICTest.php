<?php

namespace Malaysia\Identification\Tests;

use Carbon\CarbonInterface;
use Malaysia\Identification\NRIC;
use PHPUnit\Framework\TestCase;

class NRICTest extends TestCase
{
    /**
     * @test
     * @dataProvider validNricDataProvider
     */
    public function it_can_initiate_valid_identification_numbers($given, $formatted, $placeOfBirthCode, $genderCode, $asArray)
    {
        $nric = NRIC::given($given);

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
        $nric = NRIC::given($given);

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

        foreach ($nrics as $nric) {
            yield [
                \implode('', $nric),
                \implode('-', $nric),
                $nric[1],
                $nric[2],
                $nric,
            ];
        }
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

        foreach ($nrics as $nric) {
            yield [
                \implode('', $nric),
                \implode('-', $nric),
            ];
        }
    }

    /** @test */
    public function it_can_serialize_nric()
    {
        $nric = NRIC::given('810102081110');

        $this->assertSame(
            'C:28:"Malaysia\Identification\NRIC":97:{a:3:{s:9:"birthDate";s:6:"810102";s:16:"placeOfBirthCode";s:2:"08";s:10:"genderCode";s:4:"1110";}}',
            \serialize($nric)
        );
    }

    /** @test */
    public function it_can_unserialize_nric()
    {
        $nric = \unserialize(
            'C:28:"Malaysia\Identification\NRIC":97:{a:3:{s:9:"birthDate";s:6:"810102";s:16:"placeOfBirthCode";s:2:"08";s:10:"genderCode";s:4:"1110";}}'
        );

        $this->assertTrue($nric->isValid());
        $this->assertSame('810102-08-1110', $nric->toFormattedString());
        $this->assertSame('810102081110', (string) $nric);
        $this->assertInstanceOf(CarbonInterface::class, $nric->birthDate());
        $this->assertSame('08', $nric->placeOfBirthCode());
        $this->assertSame('1110', $nric->genderCode());
        $this->assertSame(['810102', '08', '1110'], $nric->toArray());
    }
}
