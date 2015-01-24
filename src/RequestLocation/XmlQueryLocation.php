<?php

namespace Otg\Ean\RequestLocation;

use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Operation;
use GuzzleHttp\Command\Guzzle\RequestLocation\XmlLocation;
use GuzzleHttp\Message\RequestInterface;

/**
 * Serializes XML as a query string parameter
 *
 * @package Otg\Ean\RequestLocation
 */
class XmlQueryLocation extends XmlLocation
{
    /**
     * {@inheritdoc}
     */
    public function after(
        CommandInterface $command,
        RequestInterface $request,
        Operation $operation,
        array $context
    ) {
        parent::after($command, $request, $operation, $context);

        /** @var \GuzzleHttp\Stream\StreamInterface $xml */
        $xml = $request->getBody();
        if ($xml) {
            $request->getQuery()->set('xml', $this->trimXml($xml->getContents()));
            $request->setBody(null);
        }
    }

    /**
     * Removes the declaration <?xml version="1.0"?> and trailing whitespace
     * @param $string
     * @return string
     */
    protected function trimXml($string)
    {
        return trim(substr($string, 22));
    }
}
