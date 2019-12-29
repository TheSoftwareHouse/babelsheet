<?php

namespace GDriveTranslations\Generator;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class Generator
{
    private $translator;
    private $multiDimensionArrayFactory;

    public function __construct()
    {
        $this->translator = new Translator('en_US', new MessageSelector());
        $this->translator->addLoader('xlf', new XliffFileLoader());

        $this->multiDimensionArrayFactory = new MultiDimensionArrayFactory();
    }

    public function load($resource, $locale)
    {
        $this->translator->addResource('xlf', $resource, $locale);
    }

    public function generateCsv($fileHandle, string $locale)
    {
        $translationMessages = $this->translator->getCatalogue($locale)->all('messages');

        $levels = $this->calculateLevels($translationMessages);

        $metaRow = ['###'];
        for ($i=0; $i<$levels; ++$i) {
            $metaRow[] = '>>>';
        }
        $metaRow[] = $locale;

        fputcsv($fileHandle, array_merge([''], $metaRow));

        $translationRows = $this->generateRows($translationMessages, $levels);

        foreach ($translationRows as $row) {
            fputcsv($fileHandle, array_merge([''], $row));
        }
    }

    private function calculateLevels(array $translationMessages): int
    {
        $levels = 1;
        foreach ($translationMessages as $key => $value) {
            $levelsCount = count(explode('.', $key));
            if ($levels < $levelsCount) {
                $levels = $levelsCount;
            }
        }

        return $levels;
    }

    private function generateRows($translationMessages, $maxLevel)
    {
        $multiDimension = $this->multiDimensionArrayFactory->create($translationMessages);

        foreach ($multiDimension as $key => $value) {
            yield from $this->generateSingleTranslationRow($key, $value, 1, $maxLevel);
        }
    }

    private function generateSingleTranslationRow($key, $value, $level, $maxLevel)
    {
        $row = ['']; //First, empty, column is a tag (###)
        for ($i=1; $i<$level; ++$i) {
            $row[] = '';
        }
        $row[] = $key;

        for ($j=$i; $j<$maxLevel; ++$j) {
            $row[] = '';
        }

        if (is_array($value)) {
            $row[] = '';
            yield $row;

            foreach ($value as $k => $v) {
                yield from $this->generateSingleTranslationRow($k, $v, $level+1, $maxLevel);
            }
        } else {
            $row[] = $value;
            yield $row;
        }
    }
}
