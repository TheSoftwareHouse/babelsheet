<?php

namespace GDriveTranslations\Generator;

use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    /**
     * @var Generator
     */
    private $generator;

    public function setUp()
    {
        $this->generator = new Generator();
    }

    public function testItGeneratesCsvLikeSourceData()
    {
        $this->generator->load(__DIR__ . '/../files/messages.en_US.xlf', 'en_US');

        $generatedCsv = fopen('php://memory', 'w');

        $this->generator->generateCsv($generatedCsv, 'en_US');
        rewind($generatedCsv);

        self::assertStringEqualsFile(
            __DIR__ . '/../files/source_data.csv',
            stream_get_contents($generatedCsv)
        );
    }
}
