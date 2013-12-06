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
            $roomCode = isset($room['roomTypeCode']) ? $room['roomTypeCode'] : $room['RoomType']['roomCode'];
            if ($room['rateCode'] == $rateCode && $roomCode == $roomTypeCode) {
                return $room;
            }
        }

        // sold out responses are caught before this point.
        throw new \UnexpectedValueException('The selected room does not exist');
    }

    /**
     * Returns parameters from a RoomAvailability response used in a PostReservation request
     *
     * @param  string $roomTypeCode roomTypeCode of the room being booked
     * @param  string $rateCode     rateCode of the room being booked
     * @return array
     */
    public function getReservationParameters($roomTypeCode, $rateCode)
    {
        $room = $this->getRoom($roomTypeCode, $rateCode);

        if ($room['supplierType'] == 'E') {
            $chargeableRate = $room['RateInfos'][0]['ChargeableRateInfo']['total'];
        } else {
            $chargeableRate = $room['RateInfos'][0]['ChargeableRateInfo']['maxNightlyRate'];
        }

        return array(
            'hotelId' => $this->data['hotelId'],
            'arrivalDate' => $this->data['arrivalDate'],
            'departureDate' => $this->data['departureDate'],
            'supplierType' => $room['supplierType'],
            'rateKey' => $room['RateInfos'][0]['RoomGroup'][0]['rateKey'],
            'roomTypeCode' => isset($room['roomTypeCode']) ? $room['roomTypeCode'] : $room['RoomType']['roomCode'],
            'rateCode' => $room['rateCode'],
            'chargeableRate' => $chargeableRate,
            'currencyCode' => $room['RateInfos'][0]['ChargeableRateInfo']['currencyCode']
        );
    }
}
