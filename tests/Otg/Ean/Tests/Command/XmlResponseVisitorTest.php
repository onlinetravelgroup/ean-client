<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Tests\Command;

use Otg\Ean\Command\XmlResponseVisitor as Visitor;
use Guzzle\Service\Description\Parameter;
use Guzzle\Tests\Service\Mock\Command\MockCommand;
use Guzzle\Http\Message\Response;

class ArrayFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testEnsuresArraysDropAttributes()
    {
        $visitor = new Visitor();
        $param = new Parameter(array(
            'name' => 'Items',
            'type' => 'array',
            'location' => 'xml',
            'items' => array(
                'sentAs' => 'Item',
                'type' => 'object'
            )
        ));

        $response = new Response(200, array(
            'X-Foo'          => 'bar',
            'Content-Length' => 3,
            'Content-Type'   => 'text/plain'
        ), 'Foo');

        $xml = '<wrap><Items size="1"><Item /></Items><Bar></Bar></wrap>';
        $value = json_decode(json_encode(new \SimpleXMLElement($xml)), true);
        $visitor->visit(new MockCommand(), $response, $param, $value);

        $this->assertEquals(array(
            'Bar' => array(),
            'Items' => array(
                array()
            ),
        ), $value);
    }
}
