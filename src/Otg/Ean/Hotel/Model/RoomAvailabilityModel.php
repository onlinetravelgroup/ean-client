<?php

namespace Otg\Ean\Hotel\Model;

use Guzzle\Service\Resource\Model as GuzzleModel;

class RoomAvailabilityModel extends GuzzleModel
{
    /**
     * Returns the Room element matching the roomTypeCode and rateCode or false if not found
     *
     * @param string $roomTypeCode
     * @param string $rateCode
     *
     * @return array|boolean
     */
    public function getRoom($roomTypeCode, $rateCode)
    {
        foreach ($this->data['Rooms'] as $room) {
            if ($room['rateCode'] == $rateCode && $room['roomTypeCode'] == $roomTypeCode) {
                return $room;
            }
        }

        return false;
    }
}
