<?php

namespace Otg\Ean;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Otg\Ean\RequestLocation\XmlQueryLocation;
use Otg\Ean\Subscriber\EanError;
use Otg\Ean\Subscriber\ResultClass;
use Otg\Ean\Subscriber\ContentLength;

/**
 * HotelClient object for executing commands against the EAN Hotel API
 *
 * @method \Otg\Ean\Result\HotelListResult getHotelList(array $arguments)
 * @method \Otg\Ean\Result\RoomAvailabilityResult getRoomAvailability(array $arguments)
 * @method \GuzzleHttp\Command\Model postReservation(array $arguments)
 * @method \GuzzleHttp\Command\Model getRoomCancellation(array $arguments)
 * @package Otg\Ean
 */
class HotelClient extends GuzzleClient
{
    /**
     * Gets a new HotelClient
     *
     * @param  array       $config GuzzleClient $config options
     * @return HotelClient
     */
    public static function factory($config = array())
    {
        $defaults = array(
            'request_locations' => array(
                'xml.query' => new XmlQueryLocation('xml.query')
            ),
            'defaults' => array(
                'booking_endpoint' => 'https://book.api.ean.com',
                'general_endpoint' => 'http://api.ean.com',
                'cid' => '',
                'apiKey' => '',
                'customerIpAddress' => '',
                'customerUserAgent' => '',
            )
        );

        $config += $defaults;
        $config['defaults'] += $defaults['defaults'];

        $httpClient = new HttpClient();
        $description = new Description(include(__DIR__ . '/Resources/hotel-xml-v3.php'));

        $client = new self($httpClient, $description, $config);
        $client->getEmitter()->attach(new ResultClass());
        $client->getEmitter()->attach(new EanError());
        $client->getEmitter()->attach(new ContentLength());

        return $client;
    }
}
