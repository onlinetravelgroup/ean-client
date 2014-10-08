<?php

namespace Otg\Ean\Subscriber;

use GuzzleHttp\Command\Event\ProcessEvent;
use GuzzleHttp\Event\SubscriberInterface;

/**
 * Adds an option to configure the class on service description models
 *
 * This is a workaround for configuring the model class directly in
 * GuzzleHttp\Command\Guzzle\Subscriber\ProcessResponse::onProcess()
 *
 * It needs to be triggered after ProcessResponse.
 *
 * @package Otg\Ean\Subscriber
 */
class ResultClass implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['process' => ['onProcess', 'last']];
    }

    public function onProcess(ProcessEvent $event)
    {
        // Model results are created by
        // GuzzleHttp\Command\Guzzle\Subscriber\ProcessResponse::onProcess()
        $result = $event->getResult();
        if (get_class($result) !== 'GuzzleHttp\Command\Model') {
            return;
        }

        $operation = $event->getCommand()->getOperation();
        if (!($modelName = $operation->getResponseModel())) {
            return;
        }

        $model = $operation->getServiceDescription()->getModel($modelName);
        if (!$model) {
            throw new \RuntimeException("Unknown model: {$modelName}");
        }

        $modelClass = $model->getData('class');
        if (!class_exists($modelClass)) {
            return;
        }

        $event->setResult(new $modelClass($result->toArray()));
    }

}
