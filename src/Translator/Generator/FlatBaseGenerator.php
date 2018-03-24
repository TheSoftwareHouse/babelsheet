<?php

namespace BabelSheet\Translator\Generator;

use BabelSheet\Source\TranslationData;

abstract class FlatBaseGenerator extends BaseGenerator
{
    protected function forgeKey(TranslationData $data, $i)
    {
        $line = $data->rows[$i];
        $keys = [];
        foreach ($data->metadata->keys as $key) {
            $keys[] = $line[$key];
        }

        return htmlspecialchars(implode('.', array_filter($keys)));
    }
}
