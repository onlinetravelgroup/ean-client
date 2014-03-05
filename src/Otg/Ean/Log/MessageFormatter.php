<?php

namespace Otg\Ean\Log;

use Guzzle\Http\Curl\CurlHandle;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Otg\Ean\Filter\StringFilter;

/**
 * Formats messages for logging
 *
 * Removes credit card details from request and response tokens
 * so they are not logged.
 *
 * @package Otg\Ean\Log
 */
class MessageFormatter extends \Guzzle\Log\MessageFormatter
{
    /**
     * @inheritDoc
     */
    public function format(
        RequestInterface $request,
        Response $response = null,
        CurlHandle $handle = null,
        array $customData = array()
    ) {

        $customData = array_merge(array(
            'url' => $this->mask((string) $request->getUrl()),
            'resource' => $this->mask($request->getResource()),
            'request' => $this->mask((string) $request),
            'response' => $this->mask((string) $response),
            'res_body' => $response ? $this->mask($response->getBody(true)) : '',
            'req_body' => $request instanceof EntityEnclosingRequestInterface
                    ? $this->mask((string) $request->getBody()) : '',
        ), $customData);

        return parent::format($request, $response, $handle, $customData);
    }

    protected function mask($string)
    {
        return StringFilter::maskCreditCard($string);
    }

}
