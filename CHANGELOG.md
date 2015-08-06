### 0.7.0

 * API minorRev upgraded to 30 from 28 (http://dev.ean.com/docs/api-change-log/)
 * ChargeableRateInfo.NightlyRates is now ChargeableRateInfo.NightlyRatesPerRoom 
   - NightlyRatesPerRoom is the API default name and helps distinguish it from the new ChargeableNightlyRates and ConvertedNightlyRates.

### 0.4.0

 * API minorRev upgraded to 28 from 26 (http://dev.ean.com/docs/api-change-log/)
 * Commands now return arrays instead of GuzzleHttp\Command\Model objects 
 * Removed Otg\Ean\Result\HotelListResult and Otg\Ean\ResultRoomAvailabilityResult
 * Otg\Ean\EanErrorException now extends GuzzleHttp\Command\Exception\CommandException and is no longer an \UnexpectedValueException
 * The value of sendReservationEmail no longer defaults to false
 
 Parameter names:
 
 * booking_endpoint is now bookingEndpoint
 * general_endpoint is now generalEndpoint
 * HotelList.thumbnailPath is now HotelList.thumbnailUrl
 * Surcharges.description is now Surcharges.type
 * RoomAmenities.description is now RoomAmenities.amenity
 * PropertyAmenities.description is now PropertyAmenities.amenity

### 0.3.0

  * Break: Migrated from Guzzle 3 to Guzzle 4
  * Break: HotelClient::factory() config keys (ean_cid, api_key, etc.) have been removed in favour of setting global parameter values (see examples)
  * Break: The get() method of Result/Model objects has been removed and top level properties should now be accessed using ArrayAccess
  * Break: AgentAttentionException, RecoverableException & UnrecoverableException have been removed, EanErrors now only cause EanErrorExceptions
  * Break: Guzzle ServiceBuilder no longer exists and the config for it was removed

  Name changes:
  
  * Break: Otg\Ean\Hotel\HotelClient is now Otg\Ean\HotelClient
  * Break: Otg\Ean\Hotel\Model\RoomAvailabilityModel is now Otg\Ean\Result\RoomAvailabilityResult
  * Break: Otg\Ean\Hotel\Model\HotelListResponse is now Otg\Ean\Result\HotelListResult
  * Break: Otg\Ean\Plugin\EanError\Exception\EanErrorException is now Otg\Ean\EanErrorException
  * Break: Otg\Ean\Log\MessageFormatter is now Otg\Ean\Log\Formatter
