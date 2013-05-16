<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Filter;

/**
 * Filter for manipulating arrays
 *
 * @package Otg\Ean\Filter
 */
class ArrayFilter
{
    /**
     * Creates a new one dimension array using value properties of an existing array
     *
     * @param $array
     * @param $indexProperty
     * @param $valueProperty
     * @return array
     */
    public static function reIndex($array, $indexProperty, $valueProperty)
    {
        $output = array();
        foreach ($array as $value) {
            $output[$value[$indexProperty]] = $value[$valueProperty];
        }

        return $output;
    }
}
