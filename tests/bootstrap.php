<?php

require __DIR__ . '/../vendor/autoload.php';

use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Service\Builder\ServiceBuilder;

GuzzleTestCase::setMockBasePath(__DIR__ . '/mock');

GuzzleTestCase::setServiceBuilder(
    ServiceBuilder::factory(__DIR__ . '/..' . $_SERVER['CONFIG'], array(
        'cid'   => $_SERVER['CID'],
        'key'   => $_SERVER['API_KEY'],
        'ip'    => $_SERVER['REMOTE_ADDR'],
        'agent' => $_SERVER['HTTP_USER_AGENT']
)));
