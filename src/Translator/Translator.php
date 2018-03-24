<?php

namespace BabelSheet\Translator;

use BabelSheet\Config\Config;
use BabelSheet\Config\Target;
use BabelSheet\Source\TranslationData;
use BabelSheet\Translator\Generator\GeneratorInterface;

class Translator
{
    /** @var  GeneratorInterface[] */
    private $generators;

    public function __construct()
    {
        $this->generators = [];
    }

    public function addGenerator(GeneratorInterface $generator)
    {
        $this->generators[] = $generator;

        return $this;
    }

    public function generate(TranslationData $data, Config $config)
    {
        foreach ($config->targets as $target) {
            $this->generateTarget($data, $target);
        }
    }

    private function generateTarget(TranslationData $data, Target $target)
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($target->format)) {
                $generator->generate($data, $target);
                break;
            }
        }
    }
}
