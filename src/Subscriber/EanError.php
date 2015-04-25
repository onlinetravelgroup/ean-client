<?php

namespace Otg\Ean\Subscriber;

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
        $response = $event->getResponse();
        if (!$response) {
            return;
        }

        try {
            $xml = $response->xml();
        } catch (\RuntimeException $e) {
            return;
        }

        $eanError = $xml->EanError ?: $xml->EanWsError;

        if ($eanError) {
            $e = new EanErrorException((string) $eanError->presentationMessage, $event->getTransaction());

            $e->setHandling((string) $eanError->handling);
            $e->setCategory((string) $eanError->category);
            $e->setVerboseMessage((string) $eanError->verboseMessage);
            $e->setItineraryId((string) $eanError->itineraryId);

            throw $e;
        }
    }
}
