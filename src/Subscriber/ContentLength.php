<?php

namespace Otg\Ean\Subscriber;

use GuzzleHttp\Command\Event\PrepareEvent;
use GuzzleHttp\Event\SubscriberInterface;

class ContentLength  implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['prepare' => ['onPrepare', 'last']];
    }

    public function onPrepare(PrepareEvent $event)
    {
        // no requests have a body but EAN/Akamai still requires this header
        $event->getRequest()->setHeader('Content-Length', '0');
    }
}
