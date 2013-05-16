<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Hotel;

use Guzzle\Common\Collection;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Common\Event;
use Otg\Ean\Plugin\EanError\EanErrorPlugin;

/**
 * Client object for executing commands against the EAN Hotel API
 *
 * @package Otg\Ean\Hotel
 */
class HotelClient extends Client
{

    /**
     * Gets a new HotelClient
     *
     * @param array $config
     * - cid: EAN CID
     * - key: API Key
     * - ip: Customer's IP Address
     * - agent: Customer's User Agent
     *
     * @return HotelClient|Client
     */
    public static function factory($config = array())
    {
        $default = array(
            'booking_endpoint' => 'https://book.api.ean.com',
            'general_endpoint' => 'http://api.ean.com',
        );

        $required = array('cid', 'key', 'booking_endpoint', 'general_endpoint');
        $config = Collection::fromConfig($config, $default, $required);

        $client = new self(null, $config);

        // Attach a service description to the client
        $description = ServiceDescription::factory(__DIR__ . '/Resources/hotel-xml-v3.php');
        $client->setDescription($description);

        // Add common elements to the request
        $dispatcher = $client->getEventDispatcher();
        $dispatcher->addListener('client.command.create',
            function (Event $event) use ($config) {
                $event['command']->set('booking_endpoint', $config['booking_endpoint']);
                $event['command']->set('general_endpoint', $config['general_endpoint']);
                $event['command']->set('cid', $config['cid']);
                $event['command']->set('apiKey', $config['key']);
                $event['command']->set('customerIpAddress', $config['ip']);
                $event['command']->set('customerUserAgent', $config['agent']);
            });

        $dispatcher->addSubscriber(new EanErrorPlugin());

        return $client;
    }
}
