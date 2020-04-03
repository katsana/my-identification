<?php

namespace Malaysia\Identification\Tests;

use Malaysia\Identification\PlateNumber;
use PHPUnit\Framework\TestCase;

class PlateNumberTest extends TestCase
{
    /**
     * @test
     * @dataProvider validPlateNumberDataProvider
     */
    public function it_can_initiate_valid_identification_numbers($given, $formatted, $prefix, $number, $suffix, $asArray)
    {
        $plate = PlateNumber::given($given);

        $this->assertTrue(PlateNumber::given($formatted)->isValid());

        $this->assertTrue($plate->isValid());
        $this->assertSame($given, (string) $plate);
        $this->assertSame($prefix, $plate->prefix());
        $this->assertSame((int) $number, $plate->number());
        $this->assertSame($suffix, $plate->suffix());
        $this->assertSame($asArray, $plate->toArray());
    }

    /**
     * @test
     * @dataProvider invalidPlateNumberDataProvider
     */
    public function it_can_initiate_invalid_identification_numbers($given, $formatted)
    {
        $plate = PlateNumber::given($given);

        $this->assertFalse($plate->isValid());
        $this->assertSame('', $plate->toFormattedString());
        $this->assertSame('', (string) $plate);
        $this->assertSame([], $plate->toArray());
    }

    /**
     * Valid PlateNumber data provider.
     *
     * @return array
     */
    public function validPlateNumberDataProvider()
    {
        $plateNumbers = [
            ['RIMAU', '1437'],
            ['WXG', '5633'],
            ['BAMBEE', '5633'],
            ['WA', '605', 'S'],
            ['WB', '1234', 'A'],
            ['U', '1234'],
            ['PUTRAJAYA', '1234'],
            ['PROTON', '1234'],
            ['A1M', '1000'],
            ['G1M', '1234'],
            ['K1M', '1'],
            ['K1M', '100'],
            ['PERFECT', '1'],
            ['PERFECT', '100'],
            ['T1M', '1000'],
            ['UP', '1'],
            ['UP', '999'],
            ['1M4U', '1234'],
        ];

        $data = [];

        foreach ($plateNumbers as $plateNumber) {
            yield [
                \implode('', $plateNumber),
                \implode(' ', $plateNumber),
                $plateNumber[0],
                $plateNumber[1],
                $plateNumber[2] ?? null,
                $plateNumber,
            ];
        }
    }

    /**
     * Invalid PlateNumber data provider.
     *
     * @return array
     */
    public function invalidPlateNumberDataProvider()
    {
        $plateNumbers = [
            ['A1M', '1001'],
            ['G', '1000', 'G'],
            ['K1M', '101'],
            ['PERFECT', '101'],
            ['T1M', '1001'],
            ['UP', '1000'],
            ['US', '1001'],
            ['R1MAU', '1437'],
        ];

        foreach ($plateNumbers as $plateNumber) {
            yield [
                \implode('', $plateNumber),
                \implode(' ', $plateNumber),
            ];
        }
    }

    /** @test */
    public function it_can_serialize_nric()
    {
        $nric = PlateNumber::given('RIMAU1437');

        $this->assertSame(
            'C:35:"Malaysia\Identification\PlateNumber":70:{a:3:{s:6:"prefix";s:5:"RIMAU";s:6:"number";s:4:"1437";s:6:"suffix";N;}}',
            \serialize($nric)
        );
    }

    /** @test */
    public function it_can_unserialize_nric()
    {
        $plate = \unserialize(
            'C:35:"Malaysia\Identification\PlateNumber":70:{a:3:{s:6:"prefix";s:5:"RIMAU";s:6:"number";s:4:"1437";s:6:"suffix";N;}}'
        );

        $this->assertTrue($plate->isValid());
        $this->assertSame('RIMAU 1437', $plate->toFormattedString());
        $this->assertSame('RIMAU1437', (string) $plate);
        $this->assertSame('RIMAU', $plate->prefix());
        $this->assertSame(1437, $plate->number());
        $this->assertNull($plate->suffix());
        $this->assertSame(['RIMAU', '1437'], $plate->toArray());
    }
}
