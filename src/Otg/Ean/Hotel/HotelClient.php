<?php

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
     * - ean_cid: EAN CID
     * - api_key: API Key
     * - customer_ip_address: Customer's IP Address
     * - customer_user_agent: Customer's User Agent
     *
     * @return HotelClient|Client
     */
    public static function factory($config = array())
    {
        $default = array(
            'booking_endpoint' => 'https://book.api.ean.com',
            'general_endpoint' => 'http://api.ean.com',
        );

        $required = array('ean_cid', 'api_key', 'booking_endpoint', 'general_endpoint');
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
                $event['command']->set('cid', $config['ean_cid']);
                $event['command']->set('apiKey', $config['api_key']);
                $event['command']->set('customerIpAddress', $config['customer_ip_address']);
                $event['command']->set('customerUserAgent', $config['customer_user_agent']);
            });

        $dispatcher->addSubscriber(new EanErrorPlugin());

        return $client;
    }
}
