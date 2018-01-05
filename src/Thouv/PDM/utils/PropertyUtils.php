<?php

namespace Thouv\PDM\utils;

class PropertyUtils
{
    public static function propertyToPropertyName($value)
    {
        $value = $value->getPropertyName();
    }
}