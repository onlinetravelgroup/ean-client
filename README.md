# ean-client

A PHP implementation of the EAN Hotel API.

Supports all API requests and both IP and signature authentication. Internally it uses the XML request and response types.

[![Build Status](https://travis-ci.org/onlinetravelgroup/ean-client.svg?branch=master)](https://travis-ci.org/onlinetravelgroup/ean-client)

## Usage

Each of the API services are represented by a method on the `HotelClient` object. Each API method takes parameters in a single array argument and returns the results as an array. The parameter names and structure match those of the [API spec](http://developer.ean.com/spec/).

A HotelClient object is instantiated using the `HotelClient::factory()` method

```php
<?php
require 'vendor/autoload.php';

use Otg\Ean\HotelClient;

$client = HotelClient::factory([
    'auth' => [
        'cid' => YOUR_CID,
        'apiKey' => YOUR_API_KEY,
        'secret' => YOUR_API_SECRET, // optional, omit if using IP authentication
    ],
    'defaults' => [
        'bookingEndpoint' => 'http://dev.api.ean.com',
        'generalEndpoint' => 'http://dev.api.ean.com',
        'customerIpAddress' => getenv('REMOTE_ADDR'),
        'customerUserAgent' => getenv('HTTP_USER_AGENT'),
    ]
]);
```
### Examples
#### Hotel List
http://developer.ean.com/docs/hotel-list
```php
$hotels = $client->getHotelList([
    'destinationString' => 'Montpellier France',
    'arrivalDate' => '2016-06-13',
    'departureDate' => '2016-06-27',
    'RoomGroup' => [
       ['numberOfAdults' => 2]
    ]
]);
```

#### Room Availability
http://developer.ean.com/docs/room-avail
```php
$rooms = $client->getRoomAvailability([
    'hotelId' => $hotels['HotelList'][0]['hotelId'],
    'arrivalDate' => '2016-06-13',
    'departureDate' => '2016-06-27',
    'RoomGroup' => [
        ['numberOfAdults' => 2]
    ]
]);
```

#### Book Reservation
http://developer.ean.com/docs/book-reservation
```php
$bedTypes = $rooms['Rooms'][0]['BedTypes'];
$smokingPreferences = explode(',', $rooms['Rooms'][0]['smokingPreferences']);

$reservation = $client->postReservation([
    'RoomGroup' => [[
        'numberOfAdults' => 2,
        'firstName' => 'Test',
        'lastName' => 'Test',
        'bedTypeId' => key($bedTypes),
        'smokingPreference' => $smokingPreferences[0],
    ]],
    'ReservationInfo' => [
        'email' => 'user@example.org',
        'firstName' => 'Test',
        'lastName' => 'Test',
        'homePhone' => '0312345678',
        'creditCardType' => 'CA',
        'creditCardNumber' => '5401999999999999',
        'creditCardIdentifier' => '123',
        'creditCardExpirationMonth' => '12',
        'creditCardExpirationYear' => '2099'
    ],
    'AddressInfo' => [
        'address1' => 'travelnow',
        'city' => 'travelnow',
        'stateProvinceCode' => 'VC',
        'countryCode' => 'AU',
        'postalCode' => '3000'
    ],
    'hotelId' => $rooms['hotelId'],
    'arrivalDate' => $rooms['arrivalDate'],
    'departureDate' => $rooms['departureDate'],
    'supplierType' => $rooms['Rooms'][0]['supplierType'],
    'rateKey' => $rooms['Rooms'][0]['RateInfos'][0]['RoomGroup'][0]['rateKey'],
    'roomTypeCode' => isset($rooms['Rooms'][0]['roomTypeCode']) ? $rooms['Rooms'][0]['roomTypeCode'] : $rooms['Rooms'][0]['RoomType']['roomCode'],
    'rateCode' => $rooms['Rooms'][0]['rateCode'],
    'chargeableRate' => $rooms['Rooms'][0]['RateInfos'][0]['ChargeableRateInfo']['total'],
    'currencyCode' => $rooms['Rooms'][0]['RateInfos'][0]['ChargeableRateInfo']['currencyCode']
]);
```

#### Cancel Reservation
http://developer.ean.com/docs/cancel-reservation
```php
$result = $client->getRoomCancellation([
    'itineraryId' => $reservation['itineraryId'],
    'email' => 'user@example.org',
    'confirmationNumber' => $reservation['confirmationNumbers'][0]
]);

if (isset($result['cancellationNumber'])) {
    printf("Room cancelled: %s", $result['cancellationNumber']);
}
```

## Installation

    $ composer require onlinetravelgroup/ean-client

## Requirements

 * PHP 5.4
 * php5-curl (suggested, unless you want to use a custom adapter)

## Contributing

Pull requests are welcome. Just be sure to follow the PSR-1/2 coding standards and don't make a mess.

Commit messages should follow the advice at http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html

Messy commits should be squashed until it looks like it was programmed perfectly the first time. This does not necessarily mean a single commit.

Diffs should be clean. This means the only lines with changes should be those relevant to the commit message. 

## Running the tests

    $ phpunit

## Credits

[Guzzle](http://guzzlephp.org) does most of the heavy lifting. This project is really just an elaborate Guzzle Services config.

## License

MIT
