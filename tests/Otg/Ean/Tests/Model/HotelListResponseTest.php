<?php
namespace Otg\Ean\Tests\Model;

use Otg\Ean\Hotel\Model\HotelListResponse;

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
        $response = new HotelListResponse($fixture);

        $this->assertEquals(array('hotelId' => 2000), $response->getHotel(2000));
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
        $response = new HotelListResponse($fixture);

        $this->assertSame(false, $response->getHotel(4000));
    }

}
