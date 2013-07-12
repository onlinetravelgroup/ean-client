<?php

namespace Otg\Ean\Hotel\Model;

use Guzzle\Service\Resource\Model as GuzzleModel;

class RoomAvailabilityModel extends GuzzleModel
{
    /**
     * Returns the Room element matching the roomTypeCode and rateCode
     *
     * @param string $roomTypeCode
     * @param string $rateCode
     *
     * @return array
     * @throws \UnexpectedValueException When room is not found
     */
    public function getRoom($roomTypeCode, $rateCode)
    {
        foreach ($this->data['Rooms'] as $room) {
            if ($room['rateCode'] == $rateCode && $room['roomTypeCode'] == $roomTypeCode) {
                return $room;
            }
        }

        // sold out responses are caught before this point.
        throw new \UnexpectedValueException('The selected room does not exist');
    }
}
