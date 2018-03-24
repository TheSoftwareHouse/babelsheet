<?php

namespace BabelSheet\Utils;

class Assert
{
    public static function propertyExists($object, $field)
    {
        if (!property_exists($object, $field)) {
            exit('missing property: '.$field);
        }
    }

    public static function isArray($object, $field)
    {
        if (!is_array($object->$field)) {
            exit($field.' should be an array');
        }
    }

    public static function isObject($name, $object)
    {
        if (!is_object($object)) {
            exit($name.' should be an object');
        }
    }

    public static function isString($name, $value)
    {
        if (!is_string($value)) {
            exit($name.' should be a string');
        }
    }
}
