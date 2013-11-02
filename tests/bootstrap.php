<?php

require __DIR__ . '/../vendor/autoload.php';

use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Service\Builder\ServiceBuilder;

GuzzleTestCase::setMockBasePath(__DIR__ . '/mock');

GuzzleTestCase::setServiceBuilder(
    ServiceBuilder::factory(__DIR__ . '/..' . $_SERVER['CONFIG'], array(
        'ean_cid'   => $_SERVER['CID'],
        'api_key'   => $_SERVER['API_KEY'],
        'customer_ip_address' => $_SERVER['REMOTE_ADDR'],
        'customer_user_agent' => $_SERVER['HTTP_USER_AGENT']
)));
