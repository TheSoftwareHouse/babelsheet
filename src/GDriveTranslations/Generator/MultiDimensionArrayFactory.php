<?php

namespace GDriveTranslations\Generator;

class MultiDimensionArrayFactory
{
    public function create(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $multiKeys = explode('.', $key);

            while (count($multiKeys) > 1) {
                $value = [array_pop($multiKeys) => $value];
            }

            $result = array_merge_recursive($result, [$multiKeys[0] => $value]);
        }

        return $result;
    }
}
