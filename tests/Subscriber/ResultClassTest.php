<?php

namespace Otg\Ean\Tests\Subscriber;

use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\Mock;
use Otg\Ean\Subscriber\ResultClass;

class ResultClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The class to use for the result object can be configured within the service
     * description.
     */
    public function testResultClassIsConfigurable()
    {
        $description = new Description(array(
            'operations' => array(
                'GetRoomAvailability' => array(
                    'responseModel' => 'RoomAvailabilityResponse'
                )
            ),
            'models' => array(
                'RoomAvailabilityResponse' => array(
                    'type' => 'object',
                    'class' => 'Otg\Ean\Result\RoomAvailabilityResult'
                )
            )
        ));

        $http = new \GuzzleHttp\Client();
        $client = new GuzzleClient($http, $description);
        $client->getEmitter()->attach(new ResultClass());

        $mock = new Mock(array(new Response(200)));
        $http->getEmitter()->attach($mock);

        $command = $client->getCommand('GetRoomAvailability');
        $result = $client->execute($command);

        $this->assertInstanceOf(
            'Otg\Ean\Result\RoomAvailabilityResult',
            $result
        );
    }

}
