<?php

namespace Otg\Ean;

use GuzzleHttp\Command\Exception\CommandException;

class EanErrorException extends CommandException
{
    /**
     * @var string Handling code for the error
     */
    protected $handling;

    /**
     * @var string Category code for the error
     */
    protected $category;

    /**
     * @var string Extended error message containing more technical information
     */
    protected $verboseMessage;

    /**
     * @var string Itinerary ID returned with some recoverable reservation errors
     */
    protected $itineraryId;

    public function setHandling($handling)
    {
        $this->handling = $handling;
    }

    public function getHandling()
    {
        return $this->handling;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setVerboseMessage($message)
    {
        $this->verboseMessage = $message;
    }

    public function getVerboseMessage()
    {
        return $this->verboseMessage;
    }

    public function setItineraryId($itineraryId)
    {
        $this->itineraryId = $itineraryId;
    }

    public function getItineraryId()
    {
        return $this->itineraryId;
    }
}
