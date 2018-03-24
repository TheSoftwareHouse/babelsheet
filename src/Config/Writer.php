<?php

namespace BabelSheet\Config;

class Writer
{
    public function write(Config $config, $filename)
    {
        $file = fopen($filename, 'w');
        fwrite($file, json_encode($config, JSON_PRETTY_PRINT));
        fclose($file);
    }
}
