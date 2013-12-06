<?php
namespace Otg\Ean\Tests\Model;

use Otg\Ean\Hotel\Model\RoomAvailabilityModel;

class RoomAvailabilityModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * An exception is thrown if the roomTypeCode and rateCode do not match anything in the Rooms array
     * @expectedException \UnexpectedValueException
     */
    public function testRoomNotFoundThrowsException()
    {
        $fixture = array(
            'Rooms' => array()
        );
        $model = new RoomAvailabilityModel($fixture);

        $model->getRoom('a', 'b');

    }

    /**
     * Reservation parameters are those which must be carried over from an
     * availability response when making a reservation.
     */
    public function testGetReservationParameters()
    {
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
                    'rateCode' => '200203964',
                    'roomTypeCode' => '200033993',
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

        $model = new RoomAvailabilityModel($fixture);

        $this->assertEquals(array(
                'hotelId' => '127009',
                'arrivalDate' => '02/20/2014',
                'departureDate' => '02/23/2014',
                'supplierType' => 'E',
                'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259',
                'roomTypeCode' => '200033993',
                'rateCode' => '200203964',
                'chargeableRate' => 814.26,
                'currencyCode' => 'AUD'
            ),
            $model->getReservationParameters('200033993', '200203964'));
    }

    /**
     * Hotel Collect properties return 'maxNightlyRate' instead of total as the chargeableRate
     */
    public function testHotelCollectGetReservationParameters()
    {
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
                    'rateCode' => '200203964',
                    'roomTypeCode' => '200033993',
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

        $model = new RoomAvailabilityModel($fixture);

        $this->assertEquals(array(
                'hotelId' => '127009',
                'arrivalDate' => '02/20/2014',
                'departureDate' => '02/23/2014',
                'supplierType' => 'V',
                'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259',
                'roomTypeCode' => '200033993',
                'rateCode' => '200203964',
                'chargeableRate' => 119.7,
                'currencyCode' => 'AUD'
            ),
            $model->getReservationParameters('200033993', '200203964'));
    }

    /**
     * The roomTypeCode is obtained from it's alternate location in the RoomType object
     * when not present in the hotel Room array     *
     */
    public function testGetReservationParametersWithAlternateRoomTypeCode()
    {
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
                    'rateCode' => '200203964',
                    'RoomType' => array(
                        'roomCode' => '200033993'
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

        $model = new RoomAvailabilityModel($fixture);

        $this->assertEquals(array(
                'hotelId' => '127009',
                'arrivalDate' => '02/20/2014',
                'departureDate' => '02/23/2014',
                'supplierType' => 'V',
                'rateKey' => '7a769d28-9156-4b6d-989c-b3d9522a8259',
                'roomTypeCode' => '200033993',
                'rateCode' => '200203964',
                'chargeableRate' => 119.7,
                'currencyCode' => 'AUD'
            ),
            $model->getReservationParameters('200033993', '200203964'));
    }
}
