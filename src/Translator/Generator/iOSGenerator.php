<?php

namespace BabelSheet\Translator\Generator;

use BabelSheet\Config\Target;
use BabelSheet\Source\TranslationData;

class iOSGenerator extends FlatBaseGenerator implements GeneratorInterface
{
    const DEFAULT_PATTERN = '%locale%.lproj/Localizable';
    const EXTENSION = '.strings';

    public function generate(TranslationData $data, Target $target)
    {
        $target->checkPattern(self::DEFAULT_PATTERN);
        foreach ($data->metadata->langs as $key => $lang) {
            $content = '';
            for ($i = 0; $i < count($data->rows); ++$i) {
                if ($i == count($data->rows) - 1 || $data->isLeaf($i)) {
                    if (($target->shouldOutputEmptyValues() || $data->rows[$i][$key])
                        && $data->isExported($data->rows[$i], $target->sections, $target->tags)) {
                        $content .= '"'.$this->forgeKey($data, $i).'" = "'.
                            htmlspecialchars($data->rows[$i][$key])."\";\n";
                    }
                }
            }

            $this->save($content, $lang, $target->pattern.self::EXTENSION);
        }
    }

    public function supports($format)
    {
        return $format == 'iOS';
    }
}
