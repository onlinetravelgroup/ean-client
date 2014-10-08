<?php
namespace Otg\Ean\Tests\Response;

use Otg\Ean\Result\RoomAvailabilityResult;

class RoomAvailabilityResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Individual rooms can be selected by roomTypeCode and rateCode
     */
    public function testGetSingleRoomFromAvailability()
    {
        $fixture = array(
            'Rooms' => array(
                array(
                    'roomTypeCode' => 'x',
                    'rateCode' => 'x'
                ),
                array(
                    'roomTypeCode' => 'S1D',
                    'rateCode' => 'OCY'
                )
            )
        );

        $result = new RoomAvailabilityResult($fixture);

        $room = $result->getRoom('S1D', 'OCY');

        $this->assertEquals('S1D', $room['roomTypeCode']);
        $this->assertEquals('OCY', $room['rateCode']);
    }

    /**
     * An exception is thrown if the roomTypeCode and rateCode do not match anything in the Rooms array
     * @expectedException \UnexpectedValueException
     */
    public function testRoomNotFoundThrowsException()
    {
        $fixture = array(
            'Rooms' => array()
        );

        $response = new RoomAvailabilityResult($fixture);

        $response->getRoom('a', 'b');
    }

    /**
     * Reservation parameters are those which must be carried over from an
     * availability response when making a reservation.
     */
    public function testGetReservationParameters()
    {
        $roomTypeCode = '200033993';
        $rateCode = '200203964';

        $fixture = array(
            'hotelId' => '127009',
            'arrivalDate' => '02/20/2014',
            'departureDate' => '02/23/2014',
            'Rooms' => array(
                0 => array(
                    'rateCode' => 'A',
                    'roomTypeCode' => 'B'
                ),
                1 => array(
                    'rateCode' => $rateCode,
                    'roomTypeCode' => $roomTypeCode,
                    'supplierType' => 'E',
                    'RateInfos' => array(
                        0 => array(
                            'RoomGroup' => array(
                                0 => array(
                                    'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259'
                                )
                            ),
                            'ChargeableRateInfo' => array(
                                'total' => 814.26,
                                'currencyCode' => 'AUD'
                            )
                        )
                    )
                )
            )
        );

        $response = new RoomAvailabilityResult($fixture);

        $this->assertEquals(array(
                'hotelId' => '127009',
                'arrivalDate' => '02/20/2014',
                'departureDate' => '02/23/2014',
                'supplierType' => 'E',
                'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259',
                'roomTypeCode' => $roomTypeCode,
                'rateCode' => $rateCode,
                'chargeableRate' => 814.26,
                'currencyCode' => 'AUD'
            ),
            $response->getReservationParameters(
                $roomTypeCode,
                $rateCode
            ));
    }

    /**
     * Hotel Collect reservations use 'maxNightlyRate' as the chargeableRate
     * instead of total
     */
    public function testHotelCollectGetReservationParameters()
    {
        $roomTypeCode = '200033993';
        $rateCode = '200203964';

        $fixture = array(
            'hotelId' => '127009',
            'arrivalDate' => '02/20/2014',
            'departureDate' => '02/23/2014',
            'Rooms' => array(
                0 => array(
                    'rateCode' => 'A',
                    'roomTypeCode' => 'B'
                ),
                1 => array(
                    'rateCode' => $rateCode,
                    'roomTypeCode' => $roomTypeCode,
                    'supplierType' => 'V', // Hotel Collect
                    'RateInfos' => array(
                        0 => array(
                            'RoomGroup' => array(
                                0 => array(
                                    'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259'
                                )
                            ),
                            'ChargeableRateInfo' => array(
                                'maxNightlyRate' => 119.7,
                                'currencyCode' => 'AUD'
                            )
                        )
                    )
                )
            )
        );

        $response = new RoomAvailabilityResult($fixture);

        $this->assertEquals(array(
                'hotelId' => '127009',
                'arrivalDate' => '02/20/2014',
                'departureDate' => '02/23/2014',
                'supplierType' => 'V',
                'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259',
                'roomTypeCode' => $roomTypeCode,
                'rateCode' => $rateCode,
                'chargeableRate' => 119.7,
                'currencyCode' => 'AUD'
            ),
            $response->getReservationParameters(
                $roomTypeCode,
                $rateCode
            ));
    }

    /**
     * The roomTypeCode can also be returned in RoomType.roomCode
     */
    public function testGetReservationParametersWithAlternateRoomTypeCode()
    {
        $roomTypeCode = '200033993';
        $rateCode = '200203964';

        $fixture = array(
            'hotelId' => '127009',
            'arrivalDate' => '02/20/2014',
            'departureDate' => '02/23/2014',
            'Rooms' => array(
                0 => array(
                    'rateCode' => 'A',
                    'RoomType' => array(
                        'roomCode' => 'B'
                    )
                ),
                1 => array(
                    'rateCode' => $rateCode,
                    'RoomType' => array(
                        'roomCode' => $roomTypeCode
                    ),
                    'supplierType' => 'V',
                    'RateInfos' => array(
                        0 => array(
                            'RoomGroup' => array(
                                0 => array(
                                    'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259'
                                )
                            ),
                            'ChargeableRateInfo' => array(
                                'maxNightlyRate' => 119.7,
                                'currencyCode' => 'AUD'
                            )
                        )
                    )
                )
            )
        );

        $response = new RoomAvailabilityResult($fixture);

        $this->assertEquals(array(
                'hotelId' => '127009',
                'arrivalDate' => '02/20/2014',
                'departureDate' => '02/23/2014',
                'supplierType' => 'V',
                'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259',
                'roomTypeCode' => $roomTypeCode,
                'rateCode' => $rateCode,
                'chargeableRate' => 119.7,
                'currencyCode' => 'AUD'
            ),
            $response->getReservationParameters(
                $roomTypeCode,
                $rateCode
            ));
    }
}
