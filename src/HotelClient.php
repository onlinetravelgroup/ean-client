<?php

namespace Otg\Ean;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Serializer;
use Otg\Ean\RequestLocation\XmlQueryLocation;
use Otg\Ean\Subscriber\EanError;
use Otg\Ean\Subscriber\ContentLength;
use Otg\Ean\Subscriber\Authentication;

/**
 * HotelClient object for executing commands against the EAN Hotel API
 *
 * @method array getHotelList(array $arguments)
 * @method array getRoomAvailability(array $arguments)
 * @method array postReservation(array $arguments)
 * @method array getItinerary(array $arguments)
 * @method array getRoomCancellation(array $arguments)
 * @method array getRoomImages(array $arguments)
 * @method array getPaymentTypes(array $arguments)
 * @method array getGeoSearch(array $arguments)
 * @method array getAlternateProperties(array $arguments)
 * @method array getHotelInfo(array $arguments)
 * @package Otg\Ean
 */
class HotelClient extends GuzzleClient
{
    /**
     * Gets a new HotelClient
     *
     * @param  array       $config     GuzzleHttp\Command\Guzzle\GuzzleClient $config options
     * @param  array       $httpConfig GuzzleHttp\Client $config options
     * @return HotelClient
     */
    public static function factory($config = [], $httpConfig = [])
    {
        $description = new Description(include(__DIR__ . '/Resources/hotel-xml-v3.php'));

        $defaults = [
            'serializer' => new Serializer($description, [
                'xml.query' => new XmlQueryLocation('xml.query')
            ])
        ];

        $config += $defaults;

        $httpClient = new HttpClient($httpConfig);

        $client = new self($httpClient, $description, $config);

        $emitter = $client->getEmitter();

        $emitter->attach(new ContentLength());

        if (!isset($config['throw_errors']) ||
            $config['throw_errors'] === true
        ) {
            $emitter->attach(new EanError());
        }

        if (isset($config['auth']) &&
            isset($config['auth']['apiKey']) &&
            isset($config['auth']['cid'])
        ) {
            $emitter->attach(
                new Authentication(
                    $config['auth']['apiKey'],
                    $config['auth']['cid'],
                    isset($config['auth']['secret']) ? $config['auth']['secret'] : null
                )
            );
        }

        return $client;
    }
}
