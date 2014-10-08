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