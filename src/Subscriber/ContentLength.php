<?php

namespace Otg\Ean\Subscriber;

use GuzzleHttp\Command\Event\PreparedEvent;
use GuzzleHttp\Event\SubscriberInterface;

class ContentLength  implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['prepared' => ['onPrepared', 'last']];
    }

    public function onPrepared(PreparedEvent $event)
    {
        // no requests have a body but EAN/Akamai still requires this header
        $event->getRequest()->setHeader('Content-Length', '0');
    }
}
