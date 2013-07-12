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
}
