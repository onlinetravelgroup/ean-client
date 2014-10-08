<?php

namespace Otg\Ean\Tests\Subscriber;

use DateTime;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use Otg\Ean\HotelClient;
use Otg\Ean\EanErrorException;
use Otg\Ean\Subscriber\EanError;

class EanErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * API responses containing an EanError attribute are thrown as exceptions.
     * @expectedException \Otg\Ean\EanErrorException
     */
    public function testEanErrorException()
    {
        $client = HotelClient::factory();

        $mock = new Mock(array(new Response(200, ['Content-Type' => 'application/xml'], Stream::factory(
            '<ns2:HotelRoomReservationResponse xmlns:ns2="http://v3.hotel.wsapi.ean.com/">' .
            '<EanError>' .
            '<itineraryId>117439420</itineraryId>' .
            '<handling>RECOVERABLE</handling>' .
            '<category>CREDITCARD</category>' .
            '<exceptionConditionId>1586</exceptionConditionId>' .
            '<ErrorAttributes>' .
            '<errorAttributesMap>' .
            '<entry><key>SUPPLIER_ERROR_CODE</key><value>Reservation Credit Card failure.</value></entry>' .
            '</errorAttributesMap>' .
            '</ErrorAttributes>' .
            '<presentationMessage>We\'re sorry, our system can not authenticate ' .
                'the information you have provided. An information mismatch has ' .
                'occurred. Please verify your credit card and billing information ' .
                'are correct and try again.</presentationMessage>' .
            '<verboseMessage>error.creditCardDeclined: This verbose message is ' .
                'that this is a test of the Credit Card Failure message.: ' .
                'Reservation Credit Card failure. ' .
                '&lt;TNOWERROR_ATTR>&lt;SUPPLIER_ERROR_CODE>Reservation Credit Card failure.&lt;/SUPPLIER_ERROR_CODE>&lt;/TNOWERROR_ATTR>' .
            '</verboseMessage>' .
            '<ServerInfo serverTime="22:06:19.964-0500" timestamp="1368673579" instance="127" />' .
            '</EanError>' .
            '<customerSessionId>0ABAAA7F-F68B-5913-EAB2-4D05F790418B</customerSessionId>' .
            '</ns2:HotelRoomReservationResponse>'
        ))));

        $client->getHttpClient()->getEmitter()->attach($mock);
        $client->getEmitter()->attach(new EanError());

        $command = $client->getCommand('GetRoomAvailability', array(
            'cid' => '55505',
            'apiKey' => 'cbrzfta369qwyrm9t5b8y8kf',
            'minorRev' => 26,
            'locale' => 'en_US',
            'currencyCode' => 'AUD',
            'hotelId' => 204421,
            'arrivalDate' => new DateTime('2013-05-29'),
            'departureDate' => new DateTime('2013-05-31'),
            'RoomGroup' => array(
                array(
                    'numberOfAdults' => 2,
                    'numberOfChildren' => 2,
                    'childAges' => array(7,8)
                )
            )
        ));

        $client->execute($command);
    }
}
