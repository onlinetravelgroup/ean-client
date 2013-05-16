<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Tests\Hotel;

use Guzzle\Tests\GuzzleTestCase;

class IntegrationTest extends GuzzleTestCase
{
    protected $baseParameters = array(
        'hotelId' => 204421,
        'arrivalDate' => '05/29/2013',
        'departureDate' => '05/31/2013',
        'RoomGroup' => array(
            array('numberOfAdults' => 2),
            array(
                'numberOfAdults' => 1,
                'numberOfChildren' => 2,
                'childAges' => '13,15'
            )
        )
    );

    protected $resParameters = array();

    public function setUp()
    {
        $this->resParameters = array_merge($this->baseParameters, array(
            'supplierType' => 'E',
            'roomTypeCode' => '198058',
            'rateCode' => '484072',
            'RoomGroup' => array(
                array(
                    'numberOfAdults' => 2,
                    'firstName' => 'Test',
                    'lastName' => 'Test'
                )
            ),
            'chargeableRate' => '389.0',
            'ReservationInfo' => array(
                'creditCardExpirationMonth' => '01',
                'creditCardExpirationYear' => '2016',
                'creditCardIdentifier' => '123',
                'creditCardNumber' => '4564456445644564',
                'creditCardType' => 'VI',
                'email' => 'developer@hotels.com.au',
                'firstName' => 'Test',
                'homePhone' => '0312345678',
                'lastName' => 'Test',
            ),
            'AddressInfo' => array(
                'address1' => 'travelnow',
                'city' => 'travelnow',
                'countryCode' => 'AU',
                'stateProvinceCode' => 'VC',
                'postalCode' => '3105',
            )
        ));
    }

    /**
     * The XML declaration string of <?xml version="1.0"?>\n added by SimpleXML::asXML
     * is stripped from the XML string before being appended to the URI
     */
    public function testXmlStartWithRootElement()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $request = $client->getCommand('GetRoomAvailability', $this->baseParameters)->prepare();

        $this->assertRegExp('/xml=%3CHotelRoomAvailabilityRequest/', $request->getUrl());
    }

    /**
     * The xml query parameter is valid XML containing values from the request
     */
    public function testXmlQueryContainsActualXml()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $request = $client->getCommand('GetRoomAvailability', $this->baseParameters)->prepare();

        try {
            $xml = simplexml_load_string($request->getQuery()->get('xml'));
        } catch (\Exception $e) {
            $this->fail('xml parameter does not contain valid xml: ' . $e->getMessage());
        }

        $this->assertEquals($this->baseParameters['hotelId'], (int) $xml->hotelId);
    }

    /**
     * Common elements are added to the query string alongside the xml parameter
     */
    public function testCommonElementsAreQueryParameters()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $request = $client->getCommand('GetRoomAvailability', $this->baseParameters)->prepare();

        $query = $request->getQuery();

        // values defined in phpunit.xml and hotel-v3.php
        $this->assertEquals($_SERVER['CID'],             $query->get('cid'));
        $this->assertEquals($_SERVER['API_KEY'],         $query->get('apiKey'));
        $this->assertEquals(22,                          $query->get('minorRev'));
        $this->assertEquals('en_US',                     $query->get('locale'));
        $this->assertEquals('AUD',                       $query->get('currencyCode'));
        $this->assertEquals($_SERVER['REMOTE_ADDR'],     $query->get('customerIpAddress'));
        $this->assertEquals($_SERVER['HTTP_USER_AGENT'], $query->get('customerUserAgent'));
    }

    public function testXmlContainsNoNewlines()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $request = $client->getCommand('GetRoomAvailability', $this->baseParameters)->prepare();
        $query = $request->getQuery();

        $this->assertNotContains("\n", $query->get('xml'), 'XML does not contain line feeds');
        $this->assertNotContains("\r", $query->get('xml'), 'XML does not contain carriage returns');
    }

    /**
     * Response is format agnostic
     */
    public function testXmlResponseDoesNoNeedCasting()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $this->setMockResponse($client, 'room_availability_response');

        $result = $client->getCommand('GetRoomAvailability', $this->baseParameters)->getResult();

        // SimpleXML values require casting like (int) $result->hotelId
        $this->assertEquals($this->baseParameters['hotelId'], $result['hotelId']);

    }

    /**
     * Response collections are just arrays
     */
    public function testResponseCollection()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $this->setMockResponse($client, 'room_availability_response');

        $result = $client->getCommand('GetRoomAvailability', $this->baseParameters)->getResult();

        $this->assertInternalType('array', $result->get('Rooms'));
        $this->assertEquals(200995943, $result->get('Rooms')[0]['rateCode']);
    }

    /**
     * Response XML attributes are converted to normal values
     */
    public function testXmlAttributeValuesAreNormalised()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $this->setMockResponse($client, 'room_availability_response');

        $result = $client->getCommand('GetRoomAvailability', $this->baseParameters)->getResult();

        $this->assertEquals(635.06, $result->get('Rooms')[0]['RateInfos'][0]['ChargeableRateInfo']['total']);
    }

    public function testReservationResult()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $this->setMockResponse($client, 'reservation_response');

        $result = $client->getCommand('PostReservation', $this->resParameters)->getResult();

        $this->assertEquals(645.86, $result->get('RateInfos')[0]['ChargeableRateInfo']['total']);
    }

    /**
     * EanWsError responses should thrown an Exception
     * @expectedException \Otg\Ean\Hotel\Exception\RecoverableException
     */
    public function testReservationErrorThrowsException()
    {
        $client = $this->getServiceBuilder()->get('hotel', true);
        $this->setMockResponse($client, 'reservation_error_creditcard');

        $client->getCommand('PostReservation', $this->resParameters)->getResult();
    }
}
