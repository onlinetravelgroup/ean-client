<?php
namespace Otg\Ean\Tests\Model;

use Otg\Ean\Result\HotelListResult;

class HotelListResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Individual hotels can be selected by hotelId
     */
    public function testEnsuresHotelsCanBeSelectedById()
    {
        $fixture = array(
            'HotelList' => array(
                0 => array(
                    'hotelId' => 1000
                ),
                1 => array(
                    'hotelId' => 2000
                ),
                2 => array(
                    'hotelId' => 3000
                )
            )
        );
        $response = new HotelListResult($fixture);

        $hotel = $response->getHotel(2000);

        $this->assertEquals(2000, $hotel['hotelId']);
    }

    /**
     * When the given hotelId is not found, getHotel returns false
     */
    public function testEnsuresGetHotelReturnsFalse()
    {
        $fixture = array(
            'HotelList' => array(
                0 => array(
                    'hotelId' => 1000
                ),
                1 => array(
                    'hotelId' => 2000
                ),
                2 => array(
                    'hotelId' => 3000
                )
            )
        );
        $response = new HotelListResult($fixture);

        $this->assertSame(false, $response->getHotel(4000));
    }

}
