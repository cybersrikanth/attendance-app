<?php

namespace App\Helpers;

class ArrayHelper
{

    public static function indexOf($object, $objKey, $arrKey, array $elementData)
    {
        $elementCount = count($elementData);
        for ($i = 0; $i < $elementCount; $i++) {
            if ($object[$objKey] == $elementData[$i][$arrKey]) {
                return $i;
            }
        }
        return -1;
    }
}
