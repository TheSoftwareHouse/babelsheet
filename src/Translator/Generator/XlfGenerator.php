<?php

namespace BabelSheet\Translator\Generator;

use BabelSheet\Config\Target;
use BabelSheet\Source\TranslationData;

class XlfGenerator extends FlatBaseGenerator implements GeneratorInterface
{
    const DEFAULT_PATTERN = 'messages.%locale%';
    const EXTENSION = '.xlf';

    public function generate(TranslationData $data, Target $target)
    {
        $target->checkPattern(self::DEFAULT_PATTERN);
        foreach ($data->metadata->langs as $key => $lang) {
            $node = new \SimpleXMLElement('<xliff/>');
            $node->addAttribute('version', '1.2');
            $node->addAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');
            $file = $node->addChild('file');
            $file->addAttribute('datatype', 'plaintext');
            $file->addAttribute('source-language', 'en');
            $file->addAttribute('original', 'file.ext');
            $body = $file->addChild('body');
            for ($i = 0; $i < count($data->rows); ++$i) {
                if ($i == count($data->rows) - 1 || $data->isLeaf($i)) {
                    if (($target->shouldOutputEmptyValues() || $data->rows[$i][$key])
                        && $data->isExported($data->rows[$i], $target->sections, $target->tags)) {
                        $unit = $body->addChild('trans-unit');
                        $unit->addAttribute('id', htmlspecialchars($this->forgeKey($data, $i)));
                        $unit->addChild('source', htmlspecialchars($this->forgeKey($data, $i)));
                        $unit->addChild('target', htmlspecialchars($data->rows[$i][$key]));
                    }
                }
            }
            $dom = dom_import_simplexml($node)->ownerDocument;
            $dom->formatOutput = true;

            $this->save($dom->saveXML(), $lang, $target->pattern.self::EXTENSION);
        }
    }

    public function supports($format)
    {
        return $format == 'xlf';
    }
}
