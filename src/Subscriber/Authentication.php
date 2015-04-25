<?php


namespace Otg\Ean\Subscriber;

use GuzzleHttp\Command\Event\InitEvent;
use GuzzleHttp\Event\SubscriberInterface;

class Authentication implements SubscriberInterface
{
    protected $apiKey;

    protected $cid;

    protected $secret;

    /**
     * @param string $apiKey
     * @param int $cid
     * @param string $secret Optional, omit if using IP authentication
     */
    public function __construct($apiKey, $cid, $secret = null)
    {
        $this->apiKey = $apiKey;
        $this->cid = $cid;
        $this->secret = $secret;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return ['init' => ['onInit', 'first']];
    }

    public function onInit(InitEvent $event)
    {
        $command = $event->getCommand();

        $command['cid'] = $this->cid;
        $command['apiKey'] = $this->apiKey;

        if (!empty($this->secret)) {
            $command['sig'] = $this->getSignature();
        }
    }

    protected function getTimestamp()
    {
        return gmdate('U');
    }

    protected function getSignature()
    {
        return md5($this->apiKey . $this->secret . $this->getTimestamp());
    }
}
