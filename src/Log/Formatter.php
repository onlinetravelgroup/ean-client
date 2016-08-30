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
    protected $paths;

    /**
     * Formatter constructor.
     * @param string $template Log message template
     * @param string[] $paths Request paths to apply masking to
     */
    public function __construct($template, $paths = null)
    {
        if ($paths) {
            $this->paths = $paths;
        } else {
            $this->paths = [
                '/ean-services/rs/hotel/v3/itin',
                '/ean-services/rs/hotel/v3/res',
            ];
        }

        parent::__construct($template);
    }

    /**
     * @inheritDoc
     */
    public function format(
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $error = null,
        array $customData = []
    ) {
        if (in_array($request->getPath(), $this->paths)) {
            $customData = array_merge([
                'url' => $this->mask((string) $request->getUrl()),
                'resource' => $this->mask($request->getResource()),
                'request' => $this->mask((string) $request),
                'response' => $this->mask((string) $response),
                'res_body' => $response ? $this->mask((string) $response) : 'NULL',
                'req_body' => $this->mask((string) $request->getBody()),
            ], $customData);
        }

        return parent::format($request, $response, $error, $customData);
    }

    protected function mask($string)
    {
        return StringFilter::maskCreditCard($string);
    }
}
