<?php

namespace BabelSheet\Translator\Generator;

use BabelSheet\Config\Target;
use BabelSheet\Source\TranslationData;

class AndroidGenerator extends FlatBaseGenerator implements GeneratorInterface
{
    const DEFAULT_PATTERN = 'values-%locale%/strings';
    const EXTENSION = '.xml';

    private $tempMetadata;

    public function generate(TranslationData $data, Target $target)
    {
        $target->checkPattern(self::DEFAULT_PATTERN);
        $this->fixRegions($data);

        foreach ($data->metadata->langs as $key => $lang) {
            $node = new \SimpleXMLElement('<resources/>');
            for ($i = 0; $i < count($data->rows); ++$i) {
                if ($i == count($data->rows) - 1 || $data->isLeaf($i)) {
                    if (($target->shouldOutputEmptyValues() || $data->rows[$i][$key])
                        && $data->isExported($data->rows[$i], $target->sections, $target->tags)) {
                        $row = $node->addChild('string', htmlspecialchars($data->rows[$i][$key]));
                        $row->addAttribute('name', htmlspecialchars($this->forgeKey($data, $i)));
                    }
                }
            }
            $dom = dom_import_simplexml($node)->ownerDocument;
            $dom->formatOutput = true;

            $this->save($dom->saveXML(), $lang, $target->pattern.self::EXTENSION);
            $this->revert($data);
        }
    }

    public function supports($format)
    {
        return $format == 'android';
    }

    protected function forgeKey(TranslationData $data, $i)
    {
        return str_replace(
            '-', '_',
            str_replace('.', '_', mb_strtolower(parent::forgeKey($data, $i)))
        );
    }

    private function fixRegions(TranslationData $data)
    {
        $this->tempMetadata = clone $data->metadata;
        foreach ($data->metadata->langs as $key => $lang) {
            $data->metadata->langs[$key] = str_replace('_', '-r', $lang);
        }
    }

    private function revert(TranslationData $data)
    {
        $data->metadata = $this->tempMetadata;
    }
}
