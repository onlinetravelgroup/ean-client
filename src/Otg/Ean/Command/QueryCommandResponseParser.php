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
        if ($operation->getData('alternativeModel')) {
            $alternativeModel = $operation->getData('alternativeModel');

            // extract innards from Guzzle\Service\Resource\Model, frankenstein into $alternativeModel
            return new $alternativeModel($model->toArray(), $model->getStructure());
        }

        return $model;
    }
}
