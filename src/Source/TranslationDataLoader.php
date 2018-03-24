<?php

namespace BabelSheet\Source;

class TranslationDataLoader
{
    public function load($filename)
    {
        $file = fopen($filename, 'r');

        do {
            $line = fgetcsv($file);
            if (feof($file)) {
                exit("No data to parse... exiting\n");
            }
        } while (!in_array('>>>', $line));

        $metadata = new Metadata($line);

        $lines = [];
        while ($line = fgetcsv($file)) {
            if (!$this->isEmptyLine($line)) {
                $lines[] = $line;
            }
        }
        fclose($file);

        $translationData = new TranslationData();
        $translationData->metadata = $metadata;
        $translationData->rows = $this->completeParentLevels($lines, $metadata);

        return $translationData;
    }

    private function isEmptyLine(array $line)
    {
        return '' === implode('', $line);
    }

    private function completeParentLevels(array $lines, Metadata $metadata)
    {
        $rows = [];
        $newline = array_fill($metadata->keys[0], count($metadata->keys), '');
        foreach ($lines as $line) {
            foreach ($metadata->keys as $key) {
                if ($line[$key]) {
                    $newline[$key] = $line[$key];
                    for ($i = $key + 1, $iMax = count($metadata->keys); $i <= $iMax; ++$i) {
                        $newline[$i] = '';
                    }
                }
            }
            foreach ($metadata->langs as $key => $value) {
                $newline[$key] = $line[$key];
            }
            $newline[$metadata->tags] = $line[$metadata->tags];
            ksort($newline);
            $rows[] = $newline;
        }

        return $rows;
    }
}
