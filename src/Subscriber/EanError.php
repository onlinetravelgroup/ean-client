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

        $eanError = $response->EanError ?: $response->EanWsError;

        if ($eanError) {

            $e = new EanErrorException((string) $eanError->presentationMessage);

            $e->setHandling((string) $eanError->handling);
            $e->setCategory((string) $eanError->category);
            $e->setVerboseMessage((string) $eanError->verboseMessage);
            $e->setItineraryId((string) $eanError->itineraryId);

            CommandEvents::emitError($event->getTransaction(), $e);
        }
    }
}
