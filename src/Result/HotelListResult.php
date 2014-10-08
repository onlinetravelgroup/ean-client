<?php

namespace Otg\Ean\Result;

use GuzzleHttp\Command\Model;

class HotelListResult extends Model
{
    protected $idIndex = array();

    /**
     * Retrieves a HotelList array item by hotelId property
     * @param  int        $hotelId
     * @return bool|array
     */
    public function getHotel($hotelId)
    {
        if (empty($this->idIndex)) {
            $this->idIndex = $this->createIdIndex();
        }

        if (isset($this->idIndex[$hotelId])) {
            $index = $this->idIndex[$hotelId];

            return $this->data['HotelList'][$index];
        }

        return false;
    }

    /**
     * Creates an array of HotelList keys indexed by hotelIds
     * @return array
     */
    protected function createIdIndex()
    {
        $idIndex = array();

        foreach ($this->data['HotelList'] as $i => $hotel) {
            $idIndex[$hotel['hotelId']] = $i;
        }

        return $idIndex;
    }
}
