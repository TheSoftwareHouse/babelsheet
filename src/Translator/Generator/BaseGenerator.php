<?php

namespace BabelSheet\Translator\Generator;

abstract class BaseGenerator
{
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    protected function save($content, $lang, $pattern)
    {
        $path = $this->directory;
        $parts = explode('/', $pattern);
        $file = array_pop($parts);
        foreach ($parts as $dir) {
            $path .= '/'.$dir;
            $path = str_replace('%locale%', $lang, $path);
            mkdir($path);
        }
        $filename = str_replace('%locale%', $lang, $path.'/'.$file);
        $file = fopen($filename, 'w');
        fwrite($file, $content);
        fclose($file);
    }
}
