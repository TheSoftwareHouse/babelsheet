<?php

namespace GDriveTranslations\Config;

use GDriveTranslations\Utils\Assert;

class Reader
{
    public function read($filename)
    {
        if (!file_exists($filename)) {
            exit('There is no translate.json file in your mapped volume');
        }

        try {
            $raw = json_decode(file_get_contents($filename));
        } catch (\Exception $e) {
            exit('Cannot parse translate.json - invalid format ?');
        }
        $this->validate($raw);

        return $this->forgeFromRaw($raw);
    }

    private function validate(\stdClass $raw)
    {
        Assert::propertyExists($raw, 'fileId');
        Assert::isString('fileId', $raw->fileId);
        Assert::isArray($raw, 'targets');
        foreach ($raw->targets as $target) {
            Assert::isObject('target', $target);
            Target::validate($target);
        }
    }

    private function forgeFromRaw(\stdClass $raw)
    {
        $config = new Config($raw->fileId);

        if (isset($raw->accessType) && $raw->accessType === Config::ACCESS_FILE) {
            $config->accessType = $raw->accessType;
        }

        $config->sheetName = 'production';
        if (isset($raw->sheetName)) {
            $config->sheetName = $raw->sheetName;
        }

        foreach ($raw->targets as $target) {
            $config->targets[] = Target::forgeFromRaw($target);
        }

        return $config;
    }
}
