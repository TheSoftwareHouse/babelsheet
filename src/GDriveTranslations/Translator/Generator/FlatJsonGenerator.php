<?php

namespace GDriveTranslations\Translator\Generator;

use GDriveTranslations\Config\Target;
use GDriveTranslations\Source\TranslationData;

class FlatJsonGenerator extends FlatBaseGenerator implements GeneratorInterface
{
    const DEFAULT_PATTERN = '%locale%';
    const EXTENSION = '.json';

    public function generate(TranslationData $data, Target $target)
    {
        $target->checkPattern(self::DEFAULT_PATTERN);
        $content = [];
        foreach ($data->metadata->langs as $key => $lang) {
            for ($i = 0; $i < count($data->rows); ++$i) {
                if ($i == count($data->rows) - 1 || $data->isLeaf($i)) {
                    if (($target->shouldOutputEmptyValues() || $data->rows[$i][$key])
                        && $data->isExported($data->rows[$i], $target->sections, $target->tags)) {

                        $content [$this->forgeKey($data, $i)] =
                            htmlspecialchars($data->rows[$i][$key]);
                    }
                }
            }

            $this->save(json_encode($content, JSON_PRETTY_PRINT), $lang, $target->pattern.self::EXTENSION);
        }
    }

    public function supports($format)
    {
        return $format == 'flatJson';
    }
}
