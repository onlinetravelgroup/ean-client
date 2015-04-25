<?php

namespace Otg\Ean\Log;

use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use Otg\Ean\Filter\StringFilter;

/**
 * Formats messages for logging
 *
 * Removes credit card details from request and response tokens
 * so they are not logged.
 *
 * @package Otg\Ean\Log
 */
class Formatter extends \GuzzleHttp\Subscriber\Log\Formatter
{
    /**
     * @inheritDoc
     */
    public function format(
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $error = null,
        array $customData = []
    ) {
        $customData = array_merge([
            'url' => $this->mask((string) $request->getUrl()),
            'resource' => $this->mask($request->getResource()),
            'request' => $this->mask((string) $request),
            'response' => $this->mask((string) $response),
            'res_body' => $response ? $this->mask((string) $response) : 'NULL',
            'req_body' => $this->mask((string) $request->getBody()),
        ], $customData);

        return parent::format($request, $response, $error, $customData);
    }

    protected function mask($string)
    {
        return StringFilter::maskCreditCard($string);
    }
}
