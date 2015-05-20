<?php

namespace Otg\Ean\Tests\Subscriber;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use Otg\Ean\HotelClient;
use Otg\Ean\Subscriber\CacheControl;

class CacheControlTest extends \PHPUnit_Framework_TestCase
{
    public function testResponseHasCacheControl()
    {
        $maxAge = 335;

        $matchPaths = [
            '/ean-services/rs/hotel/v3/list'
        ];

        $client = new HotelClient(new Client(), new Description([
            'operations' => [
                'GetHotelList' => [
                    'httpMethod' => 'GET',
                    'uri' => '/ean-services/rs/hotel/v3/list',
                ]
            ]
        ]));

        $client->getHttpClient()->getEmitter()->attach(new CacheControl($maxAge, $matchPaths));

        $mock = new Mock([new Response(200)]);
        $history = new History();

        $client->getHttpClient()->getEmitter()->attach($mock);
        $client->getHttpClient()->getEmitter()->attach($history);

        $command = $client->getCommand('GetHotelList');

        $client->execute($command);

        $response = $history->getLastResponse();

        $this->assertArrayHasKey('Cache-Control', $response->getHeaders());
        $this->assertEquals('max-age='.$maxAge, $response->getHeader('Cache-Control'));
    }

    public function testResponseDoesNotHaveCacheControl()
    {
        $maxAge = 335;

        $matchPaths = [
            '/ean-services/rs/hotel/v3/info'
        ];

        $client = new HotelClient(new Client(), new Description([
            'operations' => [
                'GetHotelList' => [
                    'httpMethod' => 'GET',
                    'uri' => '/ean-services/rs/hotel/v3/list',
                ]
            ]
        ]));

        $client->getHttpClient()->getEmitter()->attach(new CacheControl($maxAge, $matchPaths));

        $mock = new Mock([new Response(200)]);
        $history = new History();

        $client->getHttpClient()->getEmitter()->attach($mock);
        $client->getHttpClient()->getEmitter()->attach($history);

        $command = $client->getCommand('GetHotelList');

        $client->execute($command);

        $response = $history->getLastResponse();

        $this->assertArrayNotHasKey('Cache-Control', $response->getHeaders());
    }
}
