<?php

namespace BabelSheet\Translator\Generator;

use BabelSheet\Config\Target;
use BabelSheet\Source\TranslationData;

class JsonGenerator extends WalkGenerator implements GeneratorInterface
{
    const DEFAULT_PATTERN = '%locale%';
    const EXTENSION = '.json';

    public function generate(TranslationData $data, Target $target)
    {
        $target->checkPattern(self::DEFAULT_PATTERN);
        foreach ($data->metadata->langs as $key => $value) {
            $this->save(
                json_encode($this->walk($data, $target, 1, 0, $key), JSON_PRETTY_PRINT),
                $value,
                $target->pattern.self::EXTENSION)
            ;
        }
    }

    public function supports($format)
    {
        return $format == 'json';
    }
}
