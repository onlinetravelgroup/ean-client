<?php

namespace Otg\Ean\Tests\Hotel;

use DateTime;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use Otg\Ean\HotelClient;

class HotelClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The XML declaration string of <?xml version="1.0"?>\n added by SimpleXML::asXML
     * is stripped from the XML string before being appended to the URI
     */
    public function testXmlDeclarationRemoved()
    {
        $params = [
            'hotelId' => 204421,
            'arrivalDate' => '05/29/2013',
            'departureDate' => '05/31/2013',
            'RoomGroup' => [
                ['numberOfAdults' => 2],
                [
                    'numberOfAdults' => 1,
                    'numberOfChildren' => 2,
                    'childAges' => ['13', '15']
                ]
            ]
        ];

        $client = HotelClient::factory([
            'defaults' => [
                'cid' => '',
                'apiKey' => '',
                'customerIpAddress'  => '',
                'customerUserAgent'  => '',
            ]
        ]);
        $mock = new Mock([new Response(200)]);
        $history = new History();

        $emitter = $client->getHttpClient()->getEmitter();
        $emitter->attach($mock);
        $emitter->attach($history);

        $command = $client->getCommand('GetRoomAvailability', $params);
        $client->execute($command);

        $this->assertRegExp('/xml=%3CHotelRoomAvailabilityRequest/', $history->getLastRequest()->getUrl());
    }

    /**
     * The GetRoomAvailability command creates a valid room availability request
     */
    public function testRoomAvailabilityRequest()
    {
        $params = [
            'hotelId' => 204421,
            'arrivalDate' => new DateTime('2013-05-29'),
            'departureDate' => new DateTime('2013-05-31'),
            'RoomGroup' => [
                [
                    'numberOfAdults' => 2,
                    'numberOfChildren' => 2,
                    'childAges' => [7,8]
                ]
            ]
        ];

        $client = HotelClient::factory([
            'defaults' => [
                'cid' => '55505',
                'apiKey' => 'cbrzfta369qwyrm9t5b8y8kf',
                'minorRev' => 26,
                'locale' => 'en_US',
                'currencyCode' => 'AUD',
                'customerSessionId'  => 'x',
                'customerIpAddress'  => 'y',
                'customerUserAgent'  => 'z',
            ]
        ]);

        $mock = new Mock([new Response(200)]);
        $history = new History();

        $emitter = $client->getHttpClient()->getEmitter();
        $emitter->attach($mock);
        $emitter->attach($history);

        $command = $client->getCommand('GetRoomAvailability', $params);
        $client->execute($command);

        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('application/xml', $request->getHeader('Accept'));
        $this->assertEquals('gzip,deflate', $request->getHeader('Accept-Encoding'));

        $this->assertEquals('http://api.ean.com/ean-services/rs/hotel/v3/avail?'.
            'cid=55505' .
            '&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
            '&minorRev=26' .
            '&locale=en_US' .
            '&currencyCode=AUD' .
            '&customerSessionId=x' .
            '&customerIpAddress=y' .
            '&customerUserAgent=z' .
            '&xml=%3CHotelRoomAvailabilityRequest%3E' .
            '%3ChotelId%3E204421%3C%2FhotelId%3E' .
            '%3CarrivalDate%3E05%2F29%2F2013%3C%2FarrivalDate%3E' .
            '%3CdepartureDate%3E05%2F31%2F2013%3C%2FdepartureDate%3E' .
            '%3CRoomGroup%3E%3CRoom%3E' .
            '%3CnumberOfAdults%3E2%3C%2FnumberOfAdults%3E' .
            '%3CnumberOfChildren%3E2%3C%2FnumberOfChildren%3E' .
            '%3CchildAges%3E7%2C8%3C%2FchildAges%3E' .
            '%3C%2FRoom%3E' .
            '%3C%2FRoomGroup%3E' .
            '%3C%2FHotelRoomAvailabilityRequest%3E',
            $request->getUrl());

    }

    /**
     * The PostReservation command creates a valid reservation request
     */
    public function testPostReservationRequest()
    {
        $params = [
            'hotelId' => 204421,
            'arrivalDate' => new DateTime('2013-05-29'),
            'departureDate' => new DateTime('2013-05-31'),
            'RoomGroup' => [
                [
                    'numberOfAdults' => 2,
                    'numberOfChildren' => 2,
                    'childAges' => [7,8],
                    'firstName' => 'Test',
                    'lastName' => 'Test'
                ]
            ],
            'supplierType' => 'E',
            'roomTypeCode' => '198058',
            'rateCode' => '484072',
            'chargeableRate' => '389.0',
            'ReservationInfo' => [
                'creditCardExpirationMonth' => '01',
                'creditCardExpirationYear' => '2016',
                'creditCardIdentifier' => '123',
                'creditCardNumber' => '4564456445644564',
                'creditCardType' => 'VI',
                'email' => 'user@example.org',
                'firstName' => 'Test',
                'homePhone' => '0312345678',
                'lastName' => 'Test',
            ],
            'AddressInfo' => [
                'address1' => 'travelnow',
                'city' => 'travelnow',
                'countryCode' => 'AU',
                'stateProvinceCode' => 'VC',
                'postalCode' => '3000',
            ]
        ];

        $client = HotelClient::factory([
            'defaults' => [
                'cid' => '55505',
                'apiKey' => 'cbrzfta369qwyrm9t5b8y8kf',
                'minorRev' => 26,
                'locale' => 'en_US',
                'currencyCode' => 'AUD',
                'customerSessionId'  => 'x',
                'customerIpAddress'  => 'y',
                'customerUserAgent'  => 'z',
            ]
        ]);

        $mock = new Mock([new Response(200)]);
        $history = new History();

        $emitter = $client->getHttpClient()->getEmitter();
        $emitter->attach($mock);
        $emitter->attach($history);

        $command = $client->getCommand('PostReservation', $params);
        $client->execute($command);

        $request = $history->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('application/xml', $request->getHeader('Accept'));
        $this->assertEquals('gzip,deflate', $request->getHeader('Accept-Encoding'));

        $this->assertEquals('https://book.api.ean.com/ean-services/rs/hotel/v3/res?' .
            'cid=55505' .
            '&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
            '&minorRev=26' .
            '&locale=en_US' .
            '&currencyCode=AUD' .
            '&customerSessionId=x' .
            '&customerIpAddress=y' .
            '&customerUserAgent=z' .
            '&xml=%3CHotelRoomReservationRequest%3E' .
            '%3ChotelId%3E204421%3C%2FhotelId%3E' .
            '%3CarrivalDate%3E05%2F29%2F2013%3C%2FarrivalDate%3E' .
            '%3CdepartureDate%3E05%2F31%2F2013%3C%2FdepartureDate%3E' .
            '%3CsupplierType%3EE%3C%2FsupplierType%3E' .
            '%3CroomTypeCode%3E198058%3C%2FroomTypeCode%3E' .
            '%3CrateCode%3E484072%3C%2FrateCode%3E' .
            '%3CRoomGroup%3E' .
                '%3CRoom%3E' .
                '%3CnumberOfAdults%3E2' .
                '%3C%2FnumberOfAdults%3E' .
                '%3CnumberOfChildren%3E2%3C%2FnumberOfChildren%3E' .
                '%3CchildAges%3E7%2C8%3C%2FchildAges%3E' .
                '%3CfirstName%3ETest%3C%2FfirstName%3E' .
                '%3ClastName%3ETest%3C%2FlastName%3E' .
                '%3C%2FRoom%3E' .
            '%3C%2FRoomGroup%3E' .
            '%3CchargeableRate%3E389.0%3C%2FchargeableRate%3E' .
            '%3CsendReservationEmail%3E%3C%2FsendReservationEmail%3E' .
            '%3CReservationInfo%3E' .
                '%3CcreditCardExpirationMonth%3E01%3C%2FcreditCardExpirationMonth%3E' .
                '%3CcreditCardExpirationYear%3E2016%3C%2FcreditCardExpirationYear%3E' .
                '%3CcreditCardIdentifier%3E123%3C%2FcreditCardIdentifier%3E' .
                '%3CcreditCardNumber%3E4564456445644564%3C%2FcreditCardNumber%3E' .
                '%3CcreditCardType%3EVI%3C%2FcreditCardType%3E' .
                '%3Cemail%3Euser%40example.org%3C%2Femail%3E' .
                '%3CfirstName%3ETest%3C%2FfirstName%3E' .
                '%3ChomePhone%3E0312345678%3C%2FhomePhone%3E' .
                '%3ClastName%3ETest%3C%2FlastName%3E' .
            '%3C%2FReservationInfo%3E' .
            '%3CAddressInfo%3E' .
                '%3Caddress1%3Etravelnow%3C%2Faddress1%3E' .
                '%3Ccity%3Etravelnow%3C%2Fcity%3E' .
                '%3CcountryCode%3EAU%3C%2FcountryCode%3E' .
                '%3CstateProvinceCode%3EVC%3C%2FstateProvinceCode%3E' .
                '%3CpostalCode%3E3000%3C%2FpostalCode%3E' .
            '%3C%2FAddressInfo%3E' .
            '%3C%2FHotelRoomReservationRequest%3E',
            $request->getUrl());

    }

    /**
     * The GetRoomCancellation command creates a valid room cancellation request
     */
    public function testRoomCancellationRequest()
    {
        $params = [
            'email' => 'user@example.org',
            'confirmationNumber' => 'C1234',
            'itineraryId' => 1234,
            'reason' => 'HOC',
        ];

        $client = HotelClient::factory([
            'defaults' => [
                'cid' => '55505',
                'apiKey' => 'cbrzfta369qwyrm9t5b8y8kf',
                'minorRev' => 26,
                'locale' => 'en_US',
                'currencyCode' => 'AUD',
                'customerSessionId'  => 'x',
                'customerIpAddress'  => 'y',
                'customerUserAgent'  => 'z',
            ]
        ]);

        $mock = new Mock([new Response(200)]);
        $history = new History();

        $emitter = $client->getHttpClient()->getEmitter();
        $emitter->attach($mock);
        $emitter->attach($history);

        $command = $client->getCommand('GetRoomCancellation', $params);
        $client->execute($command);

        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('application/xml', $request->getHeader('Accept'));
        $this->assertEquals('gzip,deflate', $request->getHeader('Accept-Encoding'));

        $this->assertEquals('http://api.ean.com/ean-services/rs/hotel/v3/cancel?' .
            'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
            '&minorRev=26' .
            '&locale=en_US' .
            '&currencyCode=AUD' .
            '&customerSessionId=x' .
            '&customerIpAddress=y' .
            '&customerUserAgent=z' .
            '&xml=%3CHotelRoomCancellationRequest%3E' .
            '%3CitineraryId%3E1234%3C%2FitineraryId%3E' .
            '%3Cemail%3Euser%40example.org%3C%2Femail%3E' .
            '%3CconfirmationNumber%3EC1234%3C%2FconfirmationNumber%3E' .
            '%3Creason%3EHOC%3C%2Freason%3E' .
            '%3C%2FHotelRoomCancellationRequest%3E',
            $request->getUrl());
    }

    /**
     * The GetHotelList command creates a valid hotel list request
     */
    public function testHotelListRequest()
    {
        $params = [
            'hotelId' => 204421,
            'arrivalDate' => new DateTime('2013-05-29'),
            'departureDate' => new DateTime('2013-05-31'),
            'RoomGroup' => [
                [
                    'numberOfAdults' => 2,
                    'numberOfChildren' => 2,
                    'childAges' => [7,8]
                ]
            ]
        ];

        $client = HotelClient::factory([
            'defaults' => [
                'cid' => '55505',
                'apiKey' => 'cbrzfta369qwyrm9t5b8y8kf',
                'minorRev' => 26,
                'locale' => 'en_US',
                'currencyCode' => 'AUD',
                'customerSessionId'  => 'x',
                'customerIpAddress'  => 'y',
                'customerUserAgent'  => 'z',
            ]
        ]);

        $mock = new Mock([new Response(200)]);
        $history = new History();

        $emitter = $client->getHttpClient()->getEmitter();
        $emitter->attach($mock);
        $emitter->attach($history);

        $command = $client->getCommand('GetHotelList', $params);
        $client->execute($command);

        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('application/xml', $request->getHeader('Accept'));
        $this->assertEquals('gzip,deflate', $request->getHeader('Accept-Encoding'));

        $this->assertEquals('http://api.ean.com/ean-services/rs/hotel/v3/list?' .
            'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
            '&minorRev=26' .
            '&locale=en_US' .
            '&currencyCode=AUD' .
            '&customerSessionId=x' .
            '&customerIpAddress=y' .
            '&customerUserAgent=z' .
            '&xml=%3CHotelListRequest%3E' .
            '%3CarrivalDate%3E05%2F29%2F2013%3C%2FarrivalDate%3E' .
            '%3CdepartureDate%3E05%2F31%2F2013%3C%2FdepartureDate%3E' .
            '%3CRoomGroup%3E' .
            '%3CRoom%3E' .
            '%3CnumberOfAdults%3E2%3C%2FnumberOfAdults%3E' .
            '%3CnumberOfChildren%3E2%3C%2FnumberOfChildren%3E' .
            '%3CchildAges%3E7%2C8%3C%2FchildAges%3E' .
            '%3C%2FRoom%3E' .
            '%3C%2FRoomGroup%3E' .
            '%3C%2FHotelListRequest%3E',
            $request->getUrl());
    }

}
