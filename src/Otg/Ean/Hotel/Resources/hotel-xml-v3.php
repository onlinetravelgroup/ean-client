<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)

/**
 * Description of the EAN Hotel API
 *
 * @see Guzzle\Service\Description\ServiceDescription
 */
return array(
    'description' => 'EAN Hotel API for selling hotel reservations',
    'operations' => array(
        'AbstractOperation' => array(
            'parameters' => array(
                'general_endpoint' => array(
                    'location' => 'uri',
                    'required' => true
                ),
                'booking_endpoint' => array(
                    'location' => 'uri',
                    'required' => true
                ),
                'cid' => array(
                    'location' => 'query',
                    'required' => true
                ),
                'apiKey' => array(
                    'location' => 'query',
                    'required' => true
                ),
                'minorRev' => array(
                    'location' => 'query',
                    'required' => true,
                    'default' => '22'
                ),
                'locale' => array(
                    'location' => 'query',
                    'default' => 'en_US'
                ),
                'currencyCode' => array(
                    'location' => 'query',
                    'default' => 'AUD'
                ),
                'customerSessionId' => array(
                    'location' => 'query'
                ),
                'customerIpAddress' => array(
                    'location' => 'query',
                    'required' => true
                ),
                'customerUserAgent' => array(
                    'location' => 'query',
                    'required' => true
                ),
                'Accept' => array(
                    'location' => 'header',
                    'static' => true,
                    'default' => 'application/xml'
                ),
                'Accept-Encoding' => array(
                    'location' => 'header',
                    'default' => 'gzip,deflate'
                )
            ),
        ),
        'GetRoomAvailability' => array(
            'extends' => 'AbstractOperation',
            'httpMethod' => 'GET',
            'uri' => '{+general_endpoint}/ean-services/rs/hotel/v3/avail',
            'summary' => 'Retrieve all available rooms at a specific hotel that accommodate the provided guest count and any other criteria.',
            'responseClass' => 'RoomAvailabilityResult',
            'class' => 'Otg\Ean\\Command\\QueryCommand',
            'data' => array(
                'xmlRoot' => array(
                    'name' => 'HotelRoomAvailabilityRequest',
                ),
            ),
            'parameters' => array(
                'hotelId' => array(
                    'type' => 'numeric',
                    'required' => true,
                    'location' => 'xml.query'
                ),
                'arrivalDate' => array(
                    'type' => 'string',
                    'required' => true,
                    'location' => 'xml.query',
                    'filters' => array(
                        'Otg\Ean\Filter\StringFilter::formatUsDate'
                    )
                ),
                'departureDate' => array(
                    'type' => 'string',
                    'required' => true,
                    'location' => 'xml.query',
                    'filters' => array(
                        'Otg\Ean\Filter\StringFilter::formatUsDate'
                    )
                ),
                'numberOfBedrooms' => array(
                    'type' => 'numeric',
                    'location' => 'xml.query'
                ),
                'supplierType' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'rateKey' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'RoomGroup' => array(
                    'type' => 'array',
                    'location' => 'xml.query',
                    'items' => array(
                        'type' => 'object',
                        'sentAs' => 'Room',
                        'properties' => array(
                            'numberOfAdults' => array(
                                'type' => 'numeric',
                                'minimum' => 1,
                                'required' => true
                            ),
                            'numberOfChildren' => array(
                                'type' => 'numeric',
                            ),
                            'childAges' => array(
                                'type' => 'string'
                            )
                        )
                    )
                ),
                'roomTypeCode' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'rateCode' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'includeDetails' => array(
                    'type' => 'boolean',
                    'location' => 'xml.query'
                ),
                'includeRoomImages' => array(
                    'type' => 'boolean',
                    'location' => 'xml.query'
                ),
                'options' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
            )
        ),
        'PostReservation' => array(
            'extends' => 'AbstractOperation',
            'httpMethod' => 'POST',
            'uri' => '{+booking_endpoint}/ean-services/rs/hotel/v3/res',
            'summary' => 'Requests a reservation for the specified room(s)',
            'responseClass' => 'ReservationResult',
            'class' => 'Otg\Ean\\Command\\QueryCommand',
            'data' => array(
                'xmlRoot' => array(
                    'name' => 'HotelRoomReservationRequest',
                ),
            ),
            'parameters' => array(
                'hotelId' => array(
                    'type' => 'numeric',
                    'required' => true,
                    'location' => 'xml.query'
                ),
                'arrivalDate' => array(
                    'type' => 'string',
                    'required' => true,
                    'location' => 'xml.query',
                    'filters' => array(
                        'Otg\Ean\Filter\StringFilter::formatUsDate'
                    )
                ),
                'departureDate' => array(
                    'type' => 'string',
                    'required' => true,
                    'location' => 'xml.query',
                    'filters' => array(
                        'Otg\Ean\Filter\StringFilter::formatUsDate'
                    )
                ),
                'supplierType' => array(
                    'required' => true,
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'rateKey' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'roomTypeCode' => array(
                    'required' => true,
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'rateCode' => array(
                    'required' => true,
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'RoomGroup' => array(
                    'type' => 'array',
                    'location' => 'xml.query',
                    'required' => true,
                    'items' => array(
                        'type' => 'object',
                        'sentAs' => 'Room',
                        'properties' => array(
                            'numberOfAdults' => array(
                                'type' => 'numeric',
                                'minimum' => 1,
                                'required' => true
                            ),
                            'numberOfChildren' => array(
                                'type' => 'numeric'
                            ),
                            'childAges' => array(
                                'type' => 'string'
                            ),
                            'firstName' => array(
                                'type' => 'string',
                                'required' => true,
                                'filters' => array(
                                    array(
                                        'method' => 'substr',
                                        'args' => array('@value', '0', '25')
                                    )
                                )
                            ),
                            'lastName' => array(
                                'type' => 'string',
                                'required' => true,
                                'filters' => array(
                                    array(
                                        'method' => 'substr',
                                        'args' => array('@value', '0', '40')
                                    )
                                )
                            ),
                            'bedTypeId' => array(
                                'type' => 'numeric'
                            ),
                            'numberOfBeds' => array(
                                'type' => 'numeric'
                            ),
                            'smokingPreference' => array(
                                'type' => 'string'
                            ),
                        )
                    )
                ),
                'affiliateCustomerId' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'frequentGuestNumber' => array(
                    'type' => 'string',
                    'location' => 'xml.query'
                ),
                'itineraryId' => array(
                    'type' => 'numeric',
                    'location' => 'xml.query'
                ),
                'chargeableRate' => array(
                    'type' => 'numeric',
                    'location' => 'xml.query',
                    'required' => true
                ),
                'specialInformation' => array(
                    'type' => 'string',
                    'location' => 'xml.query',
                    'filters' => array(
                        'Otg\Ean\Filter\StringFilter::removeNewLines',
                        array(
                            'method' => 'substr',
                            'args' => array('@value', '0', '256')
                        )
                    )
                ),
                'sendReservationEmail' => array(
                    'type' => 'boolean',
                    'location' => 'xml.query',
                    'default' => false
                ),
                'ReservationInfo' => array(
                    'type' => 'object',
                    'required' => true,
                    'location' => 'xml.query',
                    'properties' => array(
                        'email' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'firstName' => array(
                            'type' => 'string',
                            'required' => true,
                            'filters' => array(
                                array(
                                    'method' => 'substr',
                                    'args' => array('@value', '0', '25')
                                )
                            )
                        ),
                        'lastName' => array(
                            'type' => 'string',
                            'required' => true,
                            'filters' => array(
                                array(
                                    'method' => 'substr',
                                    'args' => array('@value', '0', '40')
                                )
                            )
                        ),
                        'homePhone' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'workPhone' => array(
                            'type' => 'string'
                        ),
                        'extension' => array(
                            'type' => 'string',
                            'filters' => array(
                                array(
                                    'method' => 'substr',
                                    'args' => array('@value', '0', '5')
                                )
                            )
                        ),
                        'faxPhone' => array(
                            'type' => 'string'
                        ),
                        'companyName' => array(
                            'type' => 'string'
                        ),
                        'EmailItineraryAddresses' => array(
                            'type' => 'array',
                            'maxItems' => 4,
                            'items' => array(
                                'sentAs' => 'emailItineraryAddress',
                                'type' => 'string'
                            )
                        ),
                        'creditCardType' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'creditCardNumber' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'creditCardIdentifier' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'creditCardExpirationMonth' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'creditCardExpirationYear' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'creditCardPasHttpUserAgent' => array(
                            'type' => 'string'
                        ),
                        'creditCardPasHttpAccept' => array(
                            'type' => 'string'
                        ),
                        'creditCardPasPaRes' => array(
                            'type' => 'string'
                        )
                    ),
                ),
                'AddressInfo' => array(
                    'type' => 'object',
                    'location' => 'xml.query',
                    'required' => true,
                    'properties' => array(
                        'address1' => array(
                            'type' => 'string',
                            'required' => true,
                            'filters' => array(
                                array(
                                    'method' => 'substr',
                                    'args' => array('@value', '0', '28')
                                )
                            )
                        ),
                        'address2' => array(
                            'type' => 'string',
                            'filters' => array(
                                array(
                                    'method' => 'substr',
                                    'args' => array('@value', '0', '28')
                                )
                            )
                        ),
                        'address3' => array(
                            'type' => 'string',
                            'filters' => array(
                                array(
                                    'method' => 'substr',
                                    'args' => array('@value', '0', '28')
                                )
                            )
                        ),
                        'city' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'stateProvinceCode' => array(
                            'type' => 'string'
                        ),
                        'countryCode' => array(
                            'type' => 'string',
                            'required' => true
                        ),
                        'postalCode' => array(
                            'type' => 'string',
                            'required' => true,
                            'filters' => array(
                                array(
                                    'method' => 'substr',
                                    'args' => array('@value', '0', '10')
                                )
                            )
                        ),
                    )
                ),
            )
        )
    ),
    'models' => array(
        // extended by ChargeableRateInfo and ConvertedRateInfo
        'AbstractRateObject' => array(
            'type' => 'object',
            'properties' => array(
                'total' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'surchargeTotal' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'nightlyRateTotal' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'maxNightlyRate' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'currencyCode' => array(
                    'type' => 'string',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'commissionableUsdTotal' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'averageRate' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'averageBaseRate' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                // begin undocumented
                'grossProfitOnline' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                'grossProfitOffline' => array(
                    'type' => 'numeric',
                    'data' => array(
                        'xmlAttribute' => true
                    )
                ),
                // end undocumented
                'NightlyRates' => array(
                    'type' => 'array',
                    'sentAs' => 'NightlyRatesPerRoom',
                    'items' => array(
                        'sentAs' => 'NightlyRate',
                        'type' => 'object',
                        'properties' => array(
                            'promo' => array(
                                'type' => 'boolean',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            ),
                            'rate' => array(
                                'type' => 'numeric',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            ),
                            'baseRate' => array(
                                'type' => 'numeric',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            ),
                        )
                    )
                ),
                'Surcharges' => array(
                    'type' => 'array',
                    'items' => array(
                        'sentAs' => 'Surcharge',
                        'type' => 'object',
                        'properties' => array(
                            'amount' => array(
                                'type' => 'numeric',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            ),
                            'description' => array(
                                'sentAs' => 'type',
                                'type' => 'string',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            )
                        )
                    )
                )
            )
        ),
        'AbstractCancelPolicyInfoList' => array(
            'type' => 'array',
            'items' => array(
                'sentAs' => 'CancelPolicyInfo',
                'type' => 'object',
                'properties' => array(
                    'versionId' => array(
                        'type' => 'numeric'
                    ),
                    'cancelTime' => array(
                        'type' => 'string'
                    ),
                    'startWindowHours' => array(
                        'type' => 'string'
                    ),
                    'nightCount' => array(
                        'type' => 'string'
                    ),
                    'percent' => array(
                        'type' => 'string'
                    ),
                    'amount' => array(
                        'type' => 'string'
                    ),
                    'currencyCode' => array(
                        'type' => 'string'
                    ),
                    'timeZoneDescription' => array(
                        'type' => 'string'
                    ),
                )
            )
        ),
        'AbstractHotelFees' => array(
            'type' => 'array',
            'items' => array(
                'sentAs' => 'HotelFee',
                'type' => 'object',
                'properties' => array(
                    'description' => array(
                        'type' => 'string',
                        'data' => array(
                            'xmlAttribute' => true
                        )
                    ),
                    'amount' => array(
                        'type' => 'numeric',
                        'data' => array(
                            'xmlAttribute' => true
                        )
                    )
                )
            )
        ),
        'RoomAvailabilityResult' => array(
            'type' => 'object',
            'properties' => array(
                'hotelId' => array(
                    'location' => 'xml',
                    'type' => 'numeric',
                ),
                'arrivalDate' => array(
                    'location' => 'xml'
                ),
                'departureDate' => array(
                    'location' => 'xml'
                ),
                'hotelName' => array(
                    'location' => 'xml'
                ),
                'hotelAddress' => array(
                    'location' => 'xml'
                ),
                'hotelCity' => array(
                    'location' => 'xml'
                ),
                'hotelStateProvince' => array(
                    'location' => 'xml'
                ),
                'hotelCountry' => array(
                    'location' => 'xml'
                ),
                'numberOfRoomsRequested' => array(
                    'location' => 'xml'
                ),
                'checkInInstructions' => array(
                    'location' => 'xml'
                ),
                'rateKey' => array(
                    'location' => 'xml'
                ),
                'Rooms' => array(
                    'sentAs' => 'HotelRoomResponse',
                    'location' => 'xml',
                    'type' => 'array',
                    'items' => array(
                        'type' => 'object',
                        'properties' => array(
                            'policy' => array(
                                'type' => 'string',
                            ),
                            'rateCode' => array(
                                'type' => 'string'
                            ),
                            'roomTypeCode' => array(
                                'type' => 'string',
                            ),
                            'rateDescription' => array(
                                'type' => 'string',
                            ),
                            'roomTypeDescription' => array(
                                'type' => 'string',
                            ),
                            'supplierType' => array(
                                'type' => 'string',
                            ),
                            'otherInformation' => array(
                                'type' => 'string',
                            ),
                            'immediateChargeRequired' => array(
                                'type' => 'boolean',
                            ),
                            'propertyId' => array(
                                'type' => 'string',
                            ),
                            'smokingPreferences' => array(
                                'type' => 'string',
                            ),
                            'minGuestAge' => array(
                                'type' => 'numeric',
                            ),
                            'maxRoomOccupancy' => array(
                                'type' => 'numeric',
                            ),
                            'quotedOccupancy' => array(
                                'type' => 'numeric',
                            ),
                            'rateOccupancyPerRoom' => array(
                                'type' => 'numeric',
                            ),
                            'deepLink' => array(
                                'type' => 'string',
                            ),
                            'BedTypes' => array(
                                'type' => 'array',
                                'items' => array(
                                    'type' => 'object',
                                    'sentAs' => 'BedType',
                                    'properties' => array(
                                        'id' => array(
                                            'data' => array(
                                                'xmlAttribute' => true
                                            )
                                        ),
                                        'description' => array(
                                            'type' => 'string'
                                        )
                                    )
                                ),
                                'filters' => array(
                                    array(
                                        'method' => 'Otg\Ean\Filter\ArrayFilter::reIndex',
                                        'args' => array('@value', 'id', 'description')
                                    )
                                )
                            ),
                            'ValueAdds' => array(
                                'type' => 'array',
                                'items' => array(
                                    'type' => 'object',
                                    'sentAs' => 'ValueAdd',
                                    'properties' => array(
                                        'id' => array(
                                            'data' => array(
                                                'xmlAttribute' => true
                                            )
                                        ),
                                        'description' => array(
                                            'type' => 'string'
                                        )
                                    )
                                )
                            ),
                            'RoomImages' => array(
                                'type' => 'array',
                                'items' => array(
                                    'type' => 'object',
                                    'sentAs' => 'RoomImage',
                                    'properties' => array(
                                        'url' => array(
                                            'type' => 'string'
                                        )
                                    )
                                )
                            ),
                            'RoomType' => array(
                                'type' => 'object',
                                'properties' => array(
                                    'roomCode' => array(
                                        'data' => array(
                                            'xmlAttribute' => true
                                        )
                                    ),
                                    'roomTypeId' => array(
                                        'data' => array(
                                            'xmlAttribute' => true
                                        )
                                    ),
                                    'description' => array(
                                        'type' => 'string'
                                    ),
                                    'descriptionLong' => array(
                                        'type' => 'string'
                                    ),
                                    'RoomAmenities' => array(
                                        // todo: extend from abstract model (shared with HotelInfo model)
                                        'type' => 'array',
                                        'sentAs' => 'roomAmenities',
                                        'items' => array(
                                            'sentAs' => 'RoomAmenity',
                                            'type' => 'object',
                                            'properties' => array(
                                                'amenityId' => array(
                                                    'data' => array(
                                                        'xmlAttribute' => true
                                                    )
                                                ),
                                                'description' => array(
                                                    'sentAs' => 'amenity',
                                                    'type' => 'string'
                                                )
                                            )
                                        )
                                    ),
                                    'HotelDetails' => array(
                                        // todo: extend from abstract model (shared with HotelInfo model)
                                        'type' => 'object'
                                    ),
                                    'PropertyAmenities' => array(
                                        // todo: extend from abstract model (shared with HotelInfo model)
                                        'type' => 'array',
                                        'items' => array(
                                            'sentAs' => 'PropertyAmenity',
                                            'type' => 'object',
                                            'properties' => array(
                                                'amenityId' => array(
                                                    'data' => array(
                                                        'xmlAttribute' => true
                                                    )
                                                ),
                                                'description' => array(
                                                    'sentAs' => 'amenity',
                                                    'type' => 'string'
                                                )
                                            )
                                        )
                                    ),
                                    'HotelImages' => array(
                                        // todo: extend from abstract model (shared with HotelInfo model)
                                        'type' => 'array',
                                        'items' => array(
                                            'sentAs' => 'HotelImage',
                                            'type' => 'object',
                                            'properties' => array(
                                                'hotelImageId' => array(
                                                    'type' => 'numeric',
                                                ),
                                                'name' => array(
                                                    'type' => 'string'
                                                ),
                                                'category' => array(
                                                    'type' => 'numeric'
                                                ),
                                                'type' => array(
                                                    'type' => 'numeric',
                                                ),
                                                'caption' => array(
                                                    'type' => 'string'
                                                ),
                                                'url' => array(
                                                    'type' => 'string'
                                                ),
                                                'thumbnailUrl' => array(
                                                    'type' => 'string'
                                                ),
                                                'supplierId' => array(
                                                    'type' => 'numeric'
                                                ),
                                                'width' => array(
                                                    'type' => 'numeric'
                                                ),
                                                'height' => array(
                                                    'type' => 'numeric'
                                                ),
                                                'byteSize' => array(
                                                    'type' => 'numeric'
                                                ),
                                            )
                                        )
                                    )
                                )
                            ),
                            'RateInfos' => array(
                                'type' => 'array',
                                'items' => array(
                                    'sentAs' => 'RateInfo',
                                    'type' => 'object',
                                    'properties' => array(
                                        'priceBreakdown' => array(
                                            'type' => 'boolean',
                                            'data' => array(
                                                'xmlAttribute' => true
                                            )
                                        ),
                                        'promo' => array(
                                            'type' => 'boolean',
                                            'data' => array(
                                                'xmlAttribute' => true
                                            )
                                        ),
                                        'rateChange' => array(
                                            'type' => 'boolean',
                                            'data' => array(
                                                'xmlAttribute' => true
                                            )
                                        ),
                                        'promoId' => array(
                                            'type' => 'string'
                                        ),
                                        'promoDescription' => array(
                                            'type' => 'string'
                                        ),
                                        'promoDetailText' => array(
                                            'type' => 'string'
                                        ),
                                        'taxRate' => array(
                                            'type' => 'string'
                                        ),
                                        'nonRefundable' => array(
                                            'type' => 'boolean'
                                        ),
                                        'guaranteeRequired' => array(
                                            'type' => 'boolean'
                                        ),
                                        'depositRequired' => array(
                                            'type' => 'boolean'
                                        ),
                                        'deposit' => array(
                                            'sentAs' => 'Deposit',
                                            'type' => 'numeric'
                                        ),
                                        'rateType' => array(
                                            'type' => 'string'
                                        ),
                                        'currentAllotment' => array(
                                            'type' => 'numeric'
                                        ),
                                        'cancellationPolicy' => array(
                                            'type' => 'string'
                                        ),
                                        'CancelPolicyInfoList' => array(
                                            'extends' => 'AbstractCancelPolicyInfoList'
                                        ),
                                        'ChargeableRateInfo' => array(
                                            'extends' => 'AbstractRateObject'
                                        ),
                                        'ConvertedRateInfo' => array(
                                            'extends' => 'AbstractRateObject'
                                        ),
                                        'RoomGroup' => array(
                                            'type' => 'array',
                                            'items' => array(
                                                'type' => 'object',
                                                'sentAs' => 'Room',
                                                'properties' => array(
                                                    'numberOfAdults' => array(
                                                        'type' => 'numeric',
                                                    ),
                                                    'numberOfChildren' => array(
                                                        'type' => 'numeric',
                                                    ),
                                                    'childAges' => array(
                                                        'type' => 'string'
                                                    )
                                                )
                                            )
                                        ),
                                        'promoType' => array(
                                            'type' => 'string'
                                        ),
                                        'HotelFees' => array(
                                            'extends' => 'AbstractHotelFees'
                                        )
                                    )
                                )
                            )
                        ),
                    )
                ),
            )
        ),
        'ReservationResult' => array(
            'type' => 'object',
            'properties' => array(
                'itineraryId' => array(
                    'location' => 'xml',
                    'type' => 'numeric'
                ),
                'confirmationNumbers' => array(
                    'location' => 'xml',
                    'type' => 'array',
                    'items' => array(
                        'type' => 'numeric'
                    )
                ),
                'processedWithConfirmation' => array(
                    'location' => 'xml',
                    'type' => 'boolean'
                ),
                'errorText' => array(
                    'location' => 'xml',
                ),
                'hotelReplyText' => array(
                    'location' => 'xml',
                ),
                'supplierType' => array(
                    'location' => 'xml',
                ),
                'reservationStatusCode' => array(
                    'location' => 'xml',
                ),
                'existingItinerary' => array(
                    'location' => 'xml',
                    'type' => 'boolean'
                ),
                'numberOfRoomsBooked' => array(
                    'location' => 'xml',
                    'type' => 'numeric'
                ),
                'drivingDirections' => array(
                    'location' => 'xml',
                ),
                'checkInInstructions' => array(
                    'location' => 'xml',
                ),
                'arrivalDate' => array(
                    'location' => 'xml',
                ),
                'departureDate' => array(
                    'location' => 'xml',
                ),
                'hotelName' => array(
                    'location' => 'xml',
                ),
                'hotelAddress' => array(
                    'location' => 'xml',
                ),
                'hotelCity' => array(
                    'location' => 'xml',
                ),
                'hotelStateProvinceCode' => array(
                    'location' => 'xml',
                ),
                'hotelCountryCode' => array(
                    'location' => 'xml',
                ),
                'hotelPostalCode' => array(
                    'location' => 'xml',
                ),
                'roomDescription' => array(
                    'location' => 'xml',
                ),
                'rateOccupancyPerRoom' => array(
                    'location' => 'xml',
                    'type' => 'numeric'
                ),
                'RoomGroup' => array(
                    'location' => 'xml',
                    'type' => 'array',
                    'items' => array(
                        'type' => 'object',
                        'sentAs' => 'Room',
                        'properties' => array(
                            'numberOfAdults' => array(
                                'type' => 'numeric'
                            ),
                            'numberOfChildren' => array(
                                'type' => 'numeric'
                            ),
                            'childAges' => array(
                                'type' => 'string'
                            ),
                            'firstName' => array(
                                'type' => 'string'
                            ),
                            'lastName' => array(
                                'type' => 'string'
                            ),
                            'bedTypeId' => array(
                                'type' => 'string'
                            ),
                            'numberOfBeds' => array(
                                'type' => 'numeric'
                            ),
                            'smokingPreference' => array(
                                'type' => 'string'
                            ),
                        )
                    )
                ),
                'RateInfos' => array(
                    'location' => 'xml',
                    'type' => 'array',
                    'items' => array(
                        'sentAs' => 'RateInfo',
                        'type' => 'object',
                        'properties' => array(
                            'priceBreakdown' => array(
                                'type' => 'boolean',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            ),
                            'promo' => array(
                                'type' => 'boolean',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            ),
                            'rateChange' => array(
                                'type' => 'boolean',
                                'data' => array(
                                    'xmlAttribute' => true
                                )
                            ),
                            'cancellationPolicy' => array(
                                'type' => 'string'
                            ),
                            'CancelPolicyInfoList' => array(
                                'extends' => 'AbstractCancelPolicyInfoList'
                            ),
                            'nonRefundable' => array(
                                'type' => 'boolean'
                            ),
                            'ChargeableRateInfo' => array(
                                'extends' => 'AbstractRateObject'
                            ),
                            'ConvertedRateInfo' => array(
                                'extends' => 'AbstractRateObject'
                            ),
                            'promoType' => array(
                                'type' => 'string'
                            ),
                            'depositRequired' => array(
                                'type' => 'boolean'
                            ),
                            'deposit' => array(
                                'sentAs' => 'Deposit',
                                'type' => 'numeric'
                            ),
                            'rateType' => array(
                                'type' => 'string'
                            ),
                            'HotelFees' => array(
                                'extends' => 'AbstractHotelFees'
                            ),
                            'RoomGroup' => array(
                                'type' => 'array',
                                'items' => array(
                                    'type' => 'object',
                                    'sentAs' => 'Room',
                                    'properties' => array(
                                        'numberOfAdults' => array(
                                            'type' => 'numeric'
                                        ),
                                        'numberOfChildren' => array(
                                            'type' => 'numeric',
                                        ),
                                        'childAges' => array(
                                            'type' => 'string'
                                        ),
                                        'firstName' => array(
                                            'type' => 'string'
                                        ),
                                        'lastName' => array(
                                            'type' => 'string'
                                        ),
                                        'bedTypeId' => array(
                                            'type' => 'string'
                                        ),
                                        'numberOfBeds' => array(
                                            'type' => 'numeric'
                                        ),
                                        'smokingPreference' => array(
                                            'type' => 'string'
                                        )
                                    )
                                )
                            ),

                        )
                    )
                )
            )
        )
    )
);
