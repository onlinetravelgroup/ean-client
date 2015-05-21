<?php

namespace Otg\Ean\Subscriber;

use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;

/**
 * Appends Cache-Control headers to responses
 *
 * @package Otg\Ean\Subscriber
 */
class CacheControl implements SubscriberInterface
{
    protected $maxAge;

    protected $paths;

    /**
     * @param int $maxAge Number of seconds before the response expires
     * @param string[] $paths Request paths to add a Cache-Control header to
     */
    public function __construct($maxAge = 300, array $paths = [])
    {
        $this->maxAge = $maxAge;

        if ($paths) {
            $this->paths = $paths;
        } else {
            $this->paths = [
                '/ean-services/rs/hotel/v3/list',
                '/ean-services/rs/hotel/v3/roomImages',
                '/ean-services/rs/hotel/v3/paymentInfo',
                '/ean-services/rs/hotel/v3/geoSearch',
                '/ean-services/rs/hotel/v3/altProps',
                '/ean-services/rs/hotel/v3/info',
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['complete' => ['onComplete', RequestEvents::EARLY]];
    }

    public function onComplete(CompleteEvent $event)
    {
        if (in_array($event->getRequest()->getPath(), $this->paths)) {
            $response = $event->getResponse();
            $response->setHeaders([
                'Cache-Control' => sprintf('max-age=%d', $this->maxAge)
            ]);
        }
    }
}
