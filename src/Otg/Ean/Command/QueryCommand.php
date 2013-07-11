<?php

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

    protected static $xmlResponseVisitor;

    protected function init()
    {
        $this->setResponseParser(QueryCommandResponseParser::getInstance());

        if (!self::$xmlQueryVisitor) {
            self::$xmlQueryVisitor = new XmlQueryVisitor();
        }

        if (!self::$xmlResponseVisitor) {
            self::$xmlResponseVisitor = new XmlResponseVisitor();
        }

        $this->getRequestSerializer()->addVisitor('xml.query', self::$xmlQueryVisitor);

        $this->getResponseParser()->addVisitor('xml', self::$xmlResponseVisitor);
    }
}
