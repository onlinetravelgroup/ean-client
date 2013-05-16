<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Tests\Filter;

use Otg\Ean\Filter\ArrayFilter;

class ArrayFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Re-indexed arrays contain keys from values of the original array
     */
    public function testArrayReIndex()
    {
        $fixture = array(
            array(
                'id' => '13',
                'description' => 'Two Queen Beds'
            ),
            array(
                'id' => '15',
                'description' => 'One King Bed'
            )
        );

        $output = ArrayFilter::reIndex($fixture, 'id', 'description');

        $this->assertEquals(2, count($output));
        $this->assertEquals('Two Queen Beds', $output['13']);
        $this->assertEquals('One King Bed', $output['15']);
    }
}
