<?php

use BabelSheet\Source\TranslationDataLoader;
use PHPUnit\Framework\TestCase;

class TranslationDataLoaderTest extends TestCase
{
    /** @var TranslationDataLoader */
    private $translationDataLoader;

    protected function setUp()
    {
        $this->translationDataLoader = new TranslationDataLoader();
    }

    public function testItIgnoresEmptyRows()
    {
        $expectedTranslationData = $this->translationDataLoader->load(__DIR__ . '/files/data.csv');

        $translationData = $this->translationDataLoader->load(__DIR__ . '/files/data_with_empty_row.csv');

        self::assertEquals($expectedTranslationData, $translationData);
        self::assertCount(26, $translationData->rows);
    }
}
