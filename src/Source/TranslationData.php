<?php

namespace BabelSheet\Source;

class TranslationData
{
    /** @var  Metadata */
    public $metadata;
    public $rows;

    public function isLeaf($i)
    {
        return $this->level($this->rows[$i]) >= $this->level($this->rows[$i + 1]);
    }

    public function level($row)
    {
        $keys = [];
        foreach ($this->metadata->keys as $key) {
            $keys[] = $row[$key];
        }

        return count(array_filter($keys));
    }

    public function isExported($line, $sections, $requestedTags)
    {
        return $this->inExportedSection($line, $sections) || $this->inExportedTag($line, $requestedTags);
    }

    private function inExportedSection($line, $sections)
    {
        return in_array('_all', $sections) || in_array($line[$this->metadata->keys[0]], $sections);
    }

    private function inExportedTag($line, $requestedTags)
    {
        $exportedTag = false;
        if (count($requestedTags)) {
            $tags = array_filter(array_map('trim', explode(',', $line[$this->metadata->tags])));
            if (count($tags)) {
                $exportedTag = count(array_intersect($requestedTags, $tags)) > 0;
            }
        }

        return $exportedTag;
    }
}
