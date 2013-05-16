<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Command;

use Guzzle\Service\Command\OperationCommand;

/**
 * Creates a request where XML is appended to the query string
 *
 * @package Otg\Ean\Command
 */
class QueryCommand extends OperationCommand
{

    /**
     * @var XmlQueryVisitor
     */
    protected static $xmlQueryVisitor;

    protected function init()
    {

        if (!self::$xmlQueryVisitor) {
            self::$xmlQueryVisitor = new XmlQueryVisitor();
        }

        $this->getRequestSerializer()->addVisitor('xml.query', self::$xmlQueryVisitor);
    }
}
