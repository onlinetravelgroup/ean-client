# ean-client

A PHP implementation of the EAN Hotel API.

This project currently implements the Hotel List, Room Availability, Book Reservation and Cancel Reservation requests. For information on the API and possible request parameters refer to EANs official documentation, http://dev.ean.com/

## Usage

You will need an API Key and CID to run the examples. You can get them from EAN by signing up as a developer https://devsecure.ean.com/member/register

### Basic hotel search

This request returns the available hotels in Montpellier France with a room on the given dates.

```php
<?php
require 'vendor/autoload.php';

use Otg\Ean\HotelClient;

$client = HotelClient::factory([
    'defaults' => [
        'apiKey' => YOUR_API_KEY,
        'cid' => YOUR_CID,
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

When searching hotels only the cheapest room type from each hotel is returned in the result. Additional room types for a hotel can be retrieved using the RoomAvailability request.

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

$reservation = $client->postReservation(array_merge([
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
    ]],
    $rooms->getReservationParameters(
        $rooms['Rooms'][0]['roomTypeCode'],
        $rooms['Rooms'][0]['rateCode']
    )
));
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

    $ composer require 'onlinetravelgroup/ean-client:~0.3'

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