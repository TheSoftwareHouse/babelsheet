<?php

namespace GDriveTranslations\Config;

use GDriveTranslations\Utils\Assert;

class Target
{
    public $format;
    public $sections;
    public $tags;
    public $pattern;

    public static function forgeFromRaw(\stdClass $raw)
    {
        $target = new self();
        $target->format = $raw->format;
        $target->tags = $raw->tags;
        $target->pattern = property_exists($raw, 'pattern') ? $raw->pattern : '_default';
        $target->sections = property_exists($raw, 'sections') ? $raw->sections : [];

        return $target;
    }

    public static function validate(\stdClass $raw)
    {
        Assert::propertyExists($raw, 'format');
        if (property_exists($raw, 'sections')) {
            Assert::isArray($raw, 'sections');
            foreach ($raw->sections as $section) {
                Assert::isString('section name', $section);
            }
        }
        if (property_exists($raw, 'tags')) {
            Assert::isArray($raw, 'tags');
            foreach ($raw->tags as $tag) {
                Assert::isString('tag name', $tag);
            }
        }
    }

    public function checkPattern($default)
    {
        if ($this->pattern !== '_default') {
            return;
        }
        $this->pattern = $default;
    }
}
