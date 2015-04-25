<?php

namespace Otg\Ean\Tests\Subscriber;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use Otg\Ean\HotelClient;
use Otg\Ean\Subscriber\ContentLength;

class ContentLengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The Content-Length: 0 header is sent with the request even though
     * the request has no body.
     */
    public function testContentLengthHeaderExists()
    {
        $client = new HotelClient(new Client(), new Description([
            'operations' => [
                'PostReservation' => [
                    'httpMethod' => 'post'
                ]
            ]
        ]));

        $client->getEmitter()->attach(new ContentLength());

        $mock = new Mock([new Response(200)]);
        $history = new History();

        $client->getHttpClient()->getEmitter()->attach($mock);
        $client->getHttpClient()->getEmitter()->attach($history);

        $command = $client->getCommand('PostReservation');

        $client->execute($command);

        $request = $history->getLastRequest();

        $this->assertArrayHasKey('Content-Length', $request->getHeaders());
        $this->assertEquals('0', $request->getHeader('Content-Length'));
    }
}
