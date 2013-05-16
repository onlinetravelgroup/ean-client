<?php

namespace Otg\Ean\Plugin\EanError\Exception;

use Guzzle\Http\Message\Response;
use Guzzle\Plugin\ErrorResponse\ErrorResponseExceptionInterface;
use Guzzle\Service\Command\CommandInterface;

class EanErrorException extends \UnexpectedValueException implements ErrorResponseExceptionInterface
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

    /**
     * Create an exception for a command based on a command and an error response definition
     *
     * @param CommandInterface $command  Command that was sent
     * @param Response         $response The error response
     *
     * @return self
     */
    public static function fromCommand(CommandInterface $command, Response $response)
    {
        $xml = $response->xml();

        $exception = new static((string) $xml->EanWsError->presentationMessage);

        $exception->setHandling((string) $xml->EanWsError->handling);
        $exception->setCategory((string) $xml->EanWsError->category);
        $exception->setVerboseMessage((string) $xml->EanWsError->verboseMessage);
        $exception->setItineraryId((string) $xml->EanWsError->itineraryId);

        return $exception;
    }

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
