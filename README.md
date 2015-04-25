# ean-client

A PHP implementation of the EAN Hotel API.

Supports all API requests and both IP and signature authentication. Internally it uses the XML request and response types.

## Usage

To get an API key register with EAN: https://devsecure.ean.com/member/register

For testing you can use the example API key and CID with the http://dev.api.ean.com endpoint eg.

```php
use Otg\Ean\HotelClient;

$client = HotelClient::factory([
    'auth' => [
        'cid' => 55505,
        'apiKey' => 'cbrzfta369qwyrm9t5b8y8kf'
    ],
    'defaults' => [
        'bookingEndpoint' => 'http://dev.api.ean.com',
        'generalEndpoint' => 'http://dev.api.ean.com',
    ]
]);
```

### Basic hotel search

This request returns the available hotels in Montpellier France with a room on the given dates.

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
        'customerIpAddress' => getenv('REMOTE_ADDR'),
        'customerUserAgent' => getenv('HTTP_USER_AGENT'),
    ]
]);

$hotels = $client->getHotelList([
    'destinationString' => 'Montpellier France',
    'arrivalDate' => '2015-06-13',
    'departureDate' => '2015-06-27',
    'RoomGroup' => [
       ['numberOfAdults' => 2]
    ]
]);
```

### List additional rooms

Additional room types for a hotel can be retrieved using the RoomAvailability request.

```php
$rooms = $client->getRoomAvailability([
    'hotelId' => $hotels['HotelList'][0]['hotelId'],
    'arrivalDate' => '2015-06-13',
    'departureDate' => '2015-06-27',
    'RoomGroup' => [
        ['numberOfAdults' => 2]
    ]
]);
```

### Make a reservation

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

### Cancel the reservation

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

    $ composer require 'onlinetravelgroup/ean-client:~1.0@dev'

## Requirements

 * PHP 5.4
 * php5-curl (suggested, unless you want to use a custom adapter)

## Contributing

Pull requests are welcome. Just be sure to follow the PSR-1/2 coding standards and don't make a mess.

## Running the tests

    $ phpunit

## Credits

[Guzzle](http://guzzlephp.org) does most of the heavy lifting. This project is really just an elaborate Guzzle Services config.

## License

MIT
