<?php

namespace Otg\Ean\Tests\Hotel;

use Otg\Ean\HotelClient;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reservation.specialInformation cannot contain new lines.
     */
    public function testReservationSpecialInformationFiltersNewLines()
    {
        $client = HotelClient::factory();
        $param = $client->getDescription()->getOperation('PostReservation')->getParam('specialInformation');

        $this->assertEquals("I want thisAnd this", $param->filter("I want this\nAnd this"), 'specialInformation cannot contain new lines');
    }

    /**
     * Reservation.specialInformation only submits the first 256 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#specialInformation
     */
    public function testReservationMaxSpecialInformation()
    {
        $client = HotelClient::factory();
        $param = $client->getDescription()->getOperation('PostReservation')->getParam('specialInformation');

        $input = str_repeat('a', 300);

        $this->assertEquals(256, strlen($param->filter($input)), 'specialInformation cannot contain more than 256 characters');
    }

    /**
     * Room.firstName only submits the first 25 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#firstName
     */
    public function testReservationMaxFirstName()
    {
        $client = HotelClient::factory();

        $input = str_repeat('a', 300);

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('RoomGroup')->getItems()->getProperty('firstName');
        $this->assertEquals(25, strlen($param->filter($input)), 'Room.firstName cannot contain more than 25 characters');
    }

    /**
     * ReservationInfo.firstName only submits the first 25 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#firstName
     */
    public function testReservationInfoMaxFirstName()
    {
        $client = HotelClient::factory();

        $input = str_repeat('a', 300);

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('ReservationInfo')->getProperty('firstName');
        $this->assertEquals(25, strlen($param->filter($input)), 'ReservationInfo.firstName cannot contain more than 25 characters');
    }

    /**
     * Room.lastName only submits the first 40 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#firstName
     */
    public function testReservationMaxLastName()
    {
        $client = HotelClient::factory();

        $input = str_repeat('a', 300);

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('RoomGroup')->getItems()->getProperty('lastName');
        $this->assertEquals(40, strlen($param->filter($input)), 'Room.lastName cannot contain more than 40 characters');
    }

    /**
     * ReservationInfo.lastName only submits the first 40 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#lastName
     */
    public function testReservationInfoMaxLastName()
    {
        $client = HotelClient::factory();

        $input = str_repeat('a', 300);

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('ReservationInfo')->getProperty('lastName');
        $this->assertEquals(40, strlen($param->filter($input)), 'ReservationInfo.lastName cannot contain more than 40 characters');
    }

    /**
     * ReservationInfo.extension only submits the first 5 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#lastName
     */
    public function testReservationInfoMaxExtension()
    {
        $client = HotelClient::factory();

        $input = str_repeat('a', 300);

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('ReservationInfo')->getProperty('extension');
        $this->assertEquals(5, strlen($param->filter($input)), 'ReservationInfo.extension cannot contain more than 5 characters');
    }

    /**
     * AddressInfo.address parameters only submits the first 28 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#lastName
     */
    public function testAddressInfoMaxAddress()
    {
        $client = HotelClient::factory();

        $input = str_repeat('a', 300);

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('AddressInfo')->getProperty('address1');
        $this->assertEquals(28, strlen($param->filter($input)), 'AddressInfo.address1 cannot contain more than 28 characters');

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('AddressInfo')->getProperty('address2');
        $this->assertEquals(28, strlen($param->filter($input)), 'AddressInfo.address2 cannot contain more than 28 characters');

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('AddressInfo')->getProperty('address3');
        $this->assertEquals(28, strlen($param->filter($input)), 'AddressInfo.address3 cannot contain more than 28 characters');
    }

    /**
     * AddressInfo.postalCode only submits the first 10 characters.
     * @see http://dev.ean.com/docs/read/book_reservation#lastName
     */
    public function testAddressInfoMaxPostalCode()
    {
        $client = HotelClient::factory();

        $input = str_repeat('a', 300);

        $param = $client->getDescription()->getOperation('PostReservation')->getParam('AddressInfo')->getProperty('postalCode');
        $this->assertEquals(10, strlen($param->filter($input)), 'AddressInfo.postalCode cannot contain more than 10 characters');
    }
}
