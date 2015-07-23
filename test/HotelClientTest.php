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

    public function providerForTestRequestUrl()
    {
        return [
            [
                'GetHotelList',
                [
                    'arrivalDate' => new DateTime('2013-05-29'),
                    'departureDate' => new DateTime('2013-05-31'),
                    'RoomGroup' => [
                        [
                            'numberOfAdults' => 2,
                            'numberOfChildren' => 2,
                            'childAges' => [7,8]
                        ]
                    ],
                    'hotelIdList' => [9999, 9998, 9991]
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/list?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
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
                '%3ChotelIdList%3E9999%2C9998%2C9991%3C%2FhotelIdList%3E' .
                '%3C%2FHotelListRequest%3E'
            ],
            [
                'GetHotelList',
                [
                    'arrivalDate' => new DateTime('2013-05-29'),
                    'departureDate' => new DateTime('2013-05-31'),
                    'numberOfResults' => '200',
                    'RoomGroup' => [
                        [
                            'numberOfAdults' => 2,
                            'numberOfChildren' => 2,
                            'childAges' => [7,8]
                        ]
                    ],
                    'includeDetails' => true,
                    'includeHotelFeeBreakdown' => true,
                    'city' => 'Melbourne',
                    'stateProvinceCode' => 'VC',
                    'countryCode' => 'AU',
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/list?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelListRequest%3E' .
                '%3CarrivalDate%3E05%2F29%2F2013%3C%2FarrivalDate%3E' .
                '%3CdepartureDate%3E05%2F31%2F2013%3C%2FdepartureDate%3E' .
                '%3CnumberOfResults%3E200%3C%2FnumberOfResults%3E' .
                '%3CRoomGroup%3E' .
                    '%3CRoom%3E' .
                    '%3CnumberOfAdults%3E2%3C%2FnumberOfAdults%3E' .
                    '%3CnumberOfChildren%3E2%3C%2FnumberOfChildren%3E' .
                    '%3CchildAges%3E7%2C8%3C%2FchildAges%3E' .
                    '%3C%2FRoom%3E' .
                '%3C%2FRoomGroup%3E' .
                '%3CincludeDetails%3Etrue%3C%2FincludeDetails%3E' .
                '%3CincludeHotelFeeBreakdown%3Etrue%3C%2FincludeHotelFeeBreakdown%3E' .
                '%3Ccity%3EMelbourne%3C%2Fcity%3E' .
                '%3CstateProvinceCode%3EVC%3C%2FstateProvinceCode%3E' .
                '%3CcountryCode%3EAU%3C%2FcountryCode%3E' .
                '%3C%2FHotelListRequest%3E'
            ],
            [
                'GetHotelList',
                [
                    'arrivalDate' => new DateTime('2013-05-29'),
                    'departureDate' => new DateTime('2013-05-31'),
                    'numberOfResults' => '200',
                    'RoomGroup' => [
                        [
                            'numberOfAdults' => 2,
                            'numberOfChildren' => 2,
                            'childAges' => [7,8]
                        ]
                    ],
                    'includeDetails' => true,
                    'includeHotelFeeBreakdown' => true,
                    'destinationString' => 'Melbourne, Australia',
                    'includeSurrounding' => true,
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/list?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelListRequest%3E' .
                '%3CarrivalDate%3E05%2F29%2F2013%3C%2FarrivalDate%3E' .
                '%3CdepartureDate%3E05%2F31%2F2013%3C%2FdepartureDate%3E' .
                '%3CnumberOfResults%3E200%3C%2FnumberOfResults%3E' .
                '%3CRoomGroup%3E' .
                    '%3CRoom%3E' .
                    '%3CnumberOfAdults%3E2%3C%2FnumberOfAdults%3E' .
                    '%3CnumberOfChildren%3E2%3C%2FnumberOfChildren%3E' .
                    '%3CchildAges%3E7%2C8%3C%2FchildAges%3E' .
                    '%3C%2FRoom%3E' .
                '%3C%2FRoomGroup%3E' .
                '%3CincludeDetails%3Etrue%3C%2FincludeDetails%3E' .
                '%3CincludeHotelFeeBreakdown%3Etrue%3C%2FincludeHotelFeeBreakdown%3E' .
                '%3CdestinationString%3EMelbourne%2C%20Australia%3C%2FdestinationString%3E' .
                '%3CincludeSurrounding%3Etrue%3C%2FincludeSurrounding%3E' .
                '%3C%2FHotelListRequest%3E'
            ],
            [
                'GetRoomAvailability',
                [
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
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/avail?'.
                'cid=55505' .
                '&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelRoomAvailabilityRequest%3E' .
                '%3ChotelId%3E204421%3C%2FhotelId%3E' .
                '%3CarrivalDate%3E05%2F29%2F2013%3C%2FarrivalDate%3E' .
                '%3CdepartureDate%3E05%2F31%2F2013%3C%2FdepartureDate%3E' .
                '%3CRoomGroup%3E' .
                    '%3CRoom%3E' .
                    '%3CnumberOfAdults%3E2%3C%2FnumberOfAdults%3E' .
                    '%3CnumberOfChildren%3E2%3C%2FnumberOfChildren%3E' .
                    '%3CchildAges%3E7%2C8%3C%2FchildAges%3E' .
                    '%3C%2FRoom%3E' .
                '%3C%2FRoomGroup%3E' .
                '%3C%2FHotelRoomAvailabilityRequest%3E'
            ],
            [
                'PostReservation',
                [
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
                    'sendReservationEmail' => false,
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
                ],
                'POST',
                'https://book.api.ean.com/ean-services/rs/hotel/v3/res?' .
                'cid=55505' .
                '&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
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
                '%3CsendReservationEmail%3Efalse%3C%2FsendReservationEmail%3E' .
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
                '%3C%2FHotelRoomReservationRequest%3E'
            ],
            [
                'GetRoomCancellation',
                [
                    'email' => 'user@example.org',
                    'confirmationNumber' => 'C1234',
                    'itineraryId' => 1234,
                    'reason' => 'HOC',
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/cancel?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
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
                '%3C%2FHotelRoomCancellationRequest%3E'
            ],
            [
                'GetItinerary',
                [
                    'itineraryId' => 204421,
                    'email' => 'user@example.org'
                ],
                'GET',
                'https://book.api.ean.com/ean-services/rs/hotel/v3/itin?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelItineraryRequest%3E' .
                '%3CitineraryId%3E204421%3C%2FitineraryId%3E' .
                '%3Cemail%3Euser%40example.org%3C%2Femail%3E' .
                '%3C%2FHotelItineraryRequest%3E'
            ],
            [
                'GetItinerary',
                [
                    'affiliateConfirmationId' => '586E592B-E150-4560-9E75-D6C644696E5B'
                ],
                'GET',
                'https://book.api.ean.com/ean-services/rs/hotel/v3/itin?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelItineraryRequest%3E' .
                '%3CaffiliateConfirmationId%3E586E592B-E150-4560-9E75-D6C644696E5B%3C%2FaffiliateConfirmationId%3E' .
                '%3C%2FHotelItineraryRequest%3E'
            ],
            [
                'GetItinerary',
                [
                    'lastName' => 'Test',
                    'creditCardNumber' => '4564456445644564',
                    'confirmationExtras' => 'CUSTOMER_IP,ADDITIONAL_DATA_1',
                    'ItineraryQuery' => [
                        'creationDateStart' => '2015-04-30',
                        'creationDateEnd' => '2015-05-01',
                    ]
                ],
                'GET',
                'https://book.api.ean.com/ean-services/rs/hotel/v3/itin?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelItineraryRequest%3E' .
                '%3ClastName%3ETest%3C%2FlastName%3E' .
                '%3CcreditCardNumber%3E4564456445644564%3C%2FcreditCardNumber%3E' .
                '%3CconfirmationExtras%3ECUSTOMER_IP%2CADDITIONAL_DATA_1%3C%2FconfirmationExtras%3E' .
                '%3CItineraryQuery%3E' .
                    '%3CcreationDateStart%3E04%2F30%2F2015%3C%2FcreationDateStart%3E' .
                    '%3CcreationDateEnd%3E05%2F01%2F2015%3C%2FcreationDateEnd%3E' .
                '%3C%2FItineraryQuery%3E' .
                '%3C%2FHotelItineraryRequest%3E'
            ],
            [
                'GetItinerary',
                [
                    'lastName' => 'Test',
                    'creditCardNumber' => '4564456445644564',
                    'resendConfirmationEmail' => true,
                    'ItineraryQuery' => [
                        'departureDateStart' => '2015-04-30',
                        'departureDateEnd' => '2015-05-01',
                        'includeChildAffiliates' => true
                    ],
                ],
                'GET',
                'https://book.api.ean.com/ean-services/rs/hotel/v3/itin?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelItineraryRequest%3E' .
                '%3ClastName%3ETest%3C%2FlastName%3E' .
                '%3CcreditCardNumber%3E4564456445644564%3C%2FcreditCardNumber%3E' .
                '%3CresendConfirmationEmail%3Etrue%3C%2FresendConfirmationEmail%3E' .
                '%3CItineraryQuery%3E' .
                    '%3CdepartureDateStart%3E04%2F30%2F2015%3C%2FdepartureDateStart%3E' .
                    '%3CdepartureDateEnd%3E05%2F01%2F2015%3C%2FdepartureDateEnd%3E' .
                    '%3CincludeChildAffiliates%3Etrue%3C%2FincludeChildAffiliates%3E' .
                '%3C%2FItineraryQuery%3E' .
                '%3C%2FHotelItineraryRequest%3E'
            ],
            [
                'GetRoomImages',
                [
                    'hotelId' => '999999',
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/roomImages?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelRoomImageRequest%3E' .
                '%3ChotelId%3E999999%3C%2FhotelId%3E' .
                '%3C%2FHotelRoomImageRequest%3E'
            ],
            [
                'GetPaymentTypes',
                [
                    'hotelId' => '999999',
                    'supplierType' => 'E',
                    'rateType' => 'MerchantStandard',
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/paymentInfo?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelPaymentRequest%3E' .
                '%3ChotelId%3E999999%3C%2FhotelId%3E' .
                '%3CsupplierType%3EE%3C%2FsupplierType%3E' .
                '%3CrateType%3EMerchantStandard%3C%2FrateType%3E' .
                '%3C%2FHotelPaymentRequest%3E'
            ],
            [
                'GetGeoSearch',
                [
                    'type' => 'landmarks',
                    'destinationString' => 'Melbourne, Australia',
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/geoSearch?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CLocationInfoRequest%3E' .
                '%3Ctype%3Elandmarks%3C%2Ftype%3E' .
                '%3CdestinationString%3EMelbourne%2C%20Australia%3C%2FdestinationString%3E' .
                '%3C%2FLocationInfoRequest%3E'
            ],
            [
                'GetGeoSearch',
                [
                    'address' => '3730 Las Vegas Blvd. South',
                    'city' => 'Las Vegas',
                    'stateProvinceCode' => 'NV',
                    'countryCode' => 'US',
                    'postalCode' => '89109',
                    'ignoreSearchWeight' => true,
                    'useGeoCoder' => true
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/geoSearch?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CLocationInfoRequest%3E' .
                '%3Caddress%3E3730%20Las%20Vegas%20Blvd.%20South%3C%2Faddress%3E' .
                '%3Ccity%3ELas%20Vegas%3C%2Fcity%3E' .
                '%3CstateProvinceCode%3ENV%3C%2FstateProvinceCode%3E' .
                '%3CcountryCode%3EUS%3C%2FcountryCode%3E' .
                '%3CpostalCode%3E89109%3C%2FpostalCode%3E' .
                '%3CignoreSearchWeight%3Etrue%3C%2FignoreSearchWeight%3E' .
                '%3CuseGeoCoder%3Etrue%3C%2FuseGeoCoder%3E' .
                '%3C%2FLocationInfoRequest%3E'
            ],
            [
                'GetAlternateProperties',
                [
                    'originalHotelId' => 204421,
                    'arrivalDate' => new DateTime('2013-05-29'),
                    'departureDate' => new DateTime('2013-05-31'),
                    'originalAverageNightlyRate' => '124.30',
                    'priceType' => 1,
                    'RoomGroup' => [
                        [
                            'numberOfAdults' => 2,
                            'numberOfChildren' => 2,
                            'childAges' => [7,8]
                        ]
                    ]
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/altProps?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CAlternatePropertyListRequest%3E' .
                '%3CoriginalHotelId%3E204421%3C%2ForiginalHotelId%3E' .
                '%3CarrivalDate%3E05%2F29%2F2013%3C%2FarrivalDate%3E' .
                '%3CdepartureDate%3E05%2F31%2F2013%3C%2FdepartureDate%3E' .
                '%3CoriginalAverageNightlyRate%3E124.30%3C%2ForiginalAverageNightlyRate%3E' .
                '%3CpriceType%3E1%3C%2FpriceType%3E' .
                '%3CRoomGroup%3E' .
                    '%3CRoom%3E' .
                    '%3CnumberOfAdults%3E2%3C%2FnumberOfAdults%3E' .
                    '%3CnumberOfChildren%3E2%3C%2FnumberOfChildren%3E' .
                    '%3CchildAges%3E7%2C8%3C%2FchildAges%3E' .
                    '%3C%2FRoom%3E' .
                '%3C%2FRoomGroup%3E' .
                '%3C%2FAlternatePropertyListRequest%3E'
            ],
            [
                'GetHotelInfo',
                [
                    'hotelId' => 204421,
                    'options' => 'HOTEL_DETAILS,HOTEL_IMAGES',
                ],
                'GET',
                'http://api.ean.com/ean-services/rs/hotel/v3/info?' .
                'cid=55505&apiKey=cbrzfta369qwyrm9t5b8y8kf' .
                '&minorRev=30' .
                '&locale=en_US' .
                '&currencyCode=AUD' .
                '&customerSessionId=x' .
                '&customerIpAddress=y' .
                '&customerUserAgent=z' .
                '&xml=%3CHotelInformationRequest%3E' .
                '%3ChotelId%3E204421%3C%2FhotelId%3E' .
                '%3Coptions%3EHOTEL_DETAILS%2CHOTEL_IMAGES%3C%2Foptions%3E' .
                '%3C%2FHotelInformationRequest%3E'
            ],
        ];
    }

    /**
     * @dataProvider providerForTestRequestUrl
     */
    public function testRequestUrl($command, $params, $expectedMethod, $expectedUrl)
    {
        $client = HotelClient::factory([
            'auth' => [
                'cid' => '55505',
                'apiKey' => 'cbrzfta369qwyrm9t5b8y8kf',
            ],
            'defaults' => [
                'minorRev' => 30,
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

        $command = $client->getCommand($command, $params);
        $client->execute($command);

        $request = $history->getLastRequest();

        $this->assertEquals($expectedMethod, $request->getMethod());
        $this->assertEquals('application/xml', $request->getHeader('Accept'));
        $this->assertEquals('gzip,deflate', $request->getHeader('Accept-Encoding'));

        $this->assertEquals($expectedUrl, $request->getUrl());
    }
}
