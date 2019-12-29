<?php

namespace GDriveTranslations\Generator;

use PHPUnit\Framework\TestCase;

class MultiDimensionArrayFactoryTest extends TestCase
{
    /**
     * @var MultiDimensionArrayFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new MultiDimensionArrayFactory();
    }

    public function testItCreatesMultiDimensionArrayFromArrayWithCommaSeparatedKeys()
    {
        $commaSeparatedArray = [
            'A.B.C' => 'val1',
            'A.B.D' => 'val2',
            'A.E' => 'val3',
            'A.F' => 'val4',
            'G' => 'val5',
        ];

        $expectedArray = [
            'A' => [
                'B' => [
                    'C' => 'val1',
                    'D' => 'val2',
                    ],
                'E' => 'val3',
                'F' => 'val4',
                ],
            'G' => 'val5'
        ];

        $result = $this->factory->create($commaSeparatedArray);

        self::assertEquals($expectedArray, $result);
    }
}
