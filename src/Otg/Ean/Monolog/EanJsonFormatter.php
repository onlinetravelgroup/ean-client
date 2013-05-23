<?php

namespace Otg\Ean\Monolog;

use Monolog\Formatter\FormatterInterface;
use Otg\Ean\Filter\StringFilter;

/**
 * Formats an EAN request/response as JSON
 *
 * @package Otg\Ean\MonoLog
 */
class EanJsonFormatter implements FormatterInterface
{
    /**
     * Formats a log record.
     *
     * @param  array  $record A record to format
     * @return string The formatted record
     */
    public function format(array $record)
    {
        return json_encode($this->formatArray($record));
    }

    /**
     * Builds an array based on an EAN request/response record
     * @param  array $record
     * @return array
     */
    protected function formatArray(array $record)
    {
        $out = array();

        $request = $record['context']['request'];
        $response = $record['context']['response'];
        $handle = $record['context']['handle'];

        $out['hostname'] = gethostname();
        $out['timestamp'] = gmdate('c');
        $out['request'] = StringFilter::maskCreditCard((string) $request);
        $out['response_headers'] = $response->getRawHeaders();
        $out['response_body'] = StringFilter::maskCreditCard($response->getBody(true));
        $out['curl_stderr'] = $handle ? $handle->getStderr() : '';
        $out['connect_time'] = $response->getInfo('connect_time');
        $out['total_time'] = $response->getInfo('total_time');

        return $out;
    }

    /**
     * Formats a set of log records.
     *
     * @param  array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $out = array();
        foreach ($records as $record) {
            $out[] = $this->formatArray($record);
        }

        return json_encode($out);
    }

}
