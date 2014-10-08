<?php

namespace Otg\Ean\Subscriber;

use GuzzleHttp\Command\Event\CommandEvents;
use GuzzleHttp\Command\Event\ProcessEvent;
use GuzzleHttp\Event\SubscriberInterface;
use Otg\Ean\EanErrorException;

class EanError implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['process' => ['onProcess', 'first']];
    }

    public function onProcess(ProcessEvent $event)
    {
        $response = $event->getResponse()->xml();

        if (isset($response->EanError)) {
            $e = new EanErrorException((string) $response->EanError->presentationMessage);

            $e->setHandling((string) $response->EanError->handling);
            $e->setCategory((string) $response->EanError->category);
            $e->setVerboseMessage((string) $response->EanError->verboseMessage);
            $e->setItineraryId((string) $response->EanError->itineraryId);

            CommandEvents::emitError($event->getTransaction(), $e);
        }
    }
}
