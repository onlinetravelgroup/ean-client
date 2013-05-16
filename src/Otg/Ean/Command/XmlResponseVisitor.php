<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Command;

use Guzzle\Service\Command\LocationVisitor\Response;
use Guzzle\Service\Description\Parameter;

class XmlResponseVisitor extends Response\XmlVisitor
{
    protected function processArray(Parameter $param, &$value)
    {
        if (isset($value[0]) && isset($value['@attributes'])) {
            unset($value['@attributes']);
        }

        parent::processArray($param, $value);
    }
}
