# ean-client

PHP client for consuming the EAN API, based on Guzzle

## API

```php
use Otg\Ean\Hotel\HotelClient;

// instantiate the client

$config = array(
    'api_key' => 'cbrzfta369qwyrm9t5b8y8kf',
    'ean_cid' => 55505,
    'customer_ip_address' => $_SERVER['REMOTE_ADDR'],
    'customer_user_agent' => $_SERVER['HTTP_USER_AGENT'],
);

$client = HotelClient::factory($config);

// retrieve hotel availability

$hotels = $client->getCommand('GetHotelList', array(
    'city' => 'Montpellier',
    'countryCode' => 'FR',
    'arrivalDate' => '6/13/2014',
    'departureDate' => '6/27/2014',
    'RoomGroup' => array(
        array('numberOfAdults' => 2)
    )
))->getResult();
```

Other commands available at the moment are `GetRoomAvailability` and `PostReservation`.

## License

MIT