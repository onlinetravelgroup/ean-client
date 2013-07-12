<?php

namespace Otg\Ean\Command;

use Guzzle\Service\Command\OperationResponseParser;
use Guzzle\Http\Message\Response;
use Guzzle\Service\Description\Operation;
use Guzzle\Service\Resource\Model;
use Guzzle\Service\Command\CommandInterface;

class QueryCommandResponseParser extends OperationResponseParser
{

    protected function handleParsing(CommandInterface $command, Response $response, $contentType)
    {
        $model = parent::handleParsing($command, $response, $contentType);

        // check for an alternative to Guzzle\Service\Resource\Model
        $operation = $command->getOperation();
        if ($operation->getData('modelClass')) {
            $modelClass = $operation->getData('modelClass');

            // extract innards from Guzzle\Service\Resource\Model, frankenstein into $modelClass
            return new $modelClass($model->toArray(), $model->getStructure());
        }

        return $model;
    }
}
