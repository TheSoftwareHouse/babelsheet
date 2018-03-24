<?php
/**
 * Created by PhpStorm.
 * User: suro
 * Date: 18/11/2016
 * Time: 14:42.
 */

namespace BabelSheet\Translator\Generator;

use BabelSheet\Config\Target;
use BabelSheet\Source\TranslationData;

interface GeneratorInterface
{
    public function generate(TranslationData $data, Target $target);
    public function supports($format);
}
