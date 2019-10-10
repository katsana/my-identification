<?php

namespace Nric\Tests;

use Carbon\CarbonInterface;
use Nric\Nric;
use PHPUnit\Framework\TestCase;

class NricTest extends TestCase
{
    /**
     * @test
     * @dataProvider validNricDataProvider
     */
    public function it_can_initiate_valid_identification_numbers($given, $formatted, $placeOfBirthCode, $genderCode)
    {
        $nric = Nric::given($given);

        $this->assertTrue($nric->isValid());
        $this->assertSame($formatted, $nric->toFormattedString());
        $this->assertSame($given, (string) $nric);
        $this->assertInstanceOf(CarbonInterface::class, $nric->birthDate());
        $this->assertSame($placeOfBirthCode, $nric->placeOfBirthCode());
        $this->assertSame($genderCode, $nric->genderCode());
    }

    /**
     * Valid NRIC data provider.
     *
     * @return array
     */
    public function validNricDataProvider()
    {
        $data = [];
        $nrics = [
            ['810102', '08', '1110'],
            ['811231', '08', '1110'],
            ['120228', '08', '1110'], // Leap year
        ];

        foreach ($nrics as $nric) {
            $data[] = [
                \implode('', $nric),
                \implode('-', $nric),
                $nric[1],
                $nric[2],
            ];
        }

        return $data;
    }
}
