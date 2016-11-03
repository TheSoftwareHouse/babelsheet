<?php

namespace GDriveTranslations;

class XmlGenerator extends TranslationGenerator
{
    public function translate(&$full)
    {
        foreach ($this->metadata->langs as $key => $value) {
            $node = new \SimpleXMLElement('<xliff/>');
            $node->addAttribute('version', '1.2');
            $node->addAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');
            $file = $node->addChild('file');
            $file->addAttribute('datatype', 'plaintext');
            $body = $file->addChild('body');
            for ($i = 0; $i < count($full); ++$i) {
                if ($i == count($full) - 1 || $this->isLeaf($full, $i)) {
                    if ($this->isExported($full[$i])) {
                        $unit = $body->addChild('trans-unit');
                        $unit->addChild('source', $this->forgeKey($full[$i]));
                        $unit->addChild('target', $full[$i][$key]);
                    }
                }
            }
            $this->save($node, $value);
        }
    }

    public function save(\SimpleXMLElement $translation, $lang)
    {
        $dom = dom_import_simplexml($translation)->ownerDocument;
        $dom->formatOutput = true;
        $file = fopen('/lang/'.$lang.'.xml', 'w');
        fwrite($file, $dom->saveXML());
        fclose($file);
    }

    private function isExported($line)
    {
        return in_array($line[self::OUTPUT_COLUMN], ['', 'x']);
    }

    private function forgeKey($line)
    {
        return implode('.', array_filter(array_slice($line, $this->metadata->keys[0], count($this->metadata->keys))));
    }
}
