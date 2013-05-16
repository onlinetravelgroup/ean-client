<?php
// Modified from Guzzle\Tests\Plugin\ErrorResponsePluginTest

/* Guzzle portions licensed as follows
 *
 * Copyright (c) 2011 Michael Dowling, https://github.com/mtdowling <mtdowling@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Otg\Ean\Tests\Plugin;

use Otg\Ean\Plugin\EanError\EanErrorPlugin;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Tests\GuzzleTestCase;
use Guzzle\Tests\Mock\ErrorResponseMock;

/**
 * @covers \Guzzle\Plugin\ErrorResponse\EanErrorPlugin
 */
class EanErrorPluginTest extends GuzzleTestCase
{
    protected $client;

    public static function tearDownAfterClass()
    {
        self::getServer()->flush();
    }

    public function setUp()
    {
        $mockError = 'Guzzle\Tests\Mock\ErrorResponseMock';
        $description = ServiceDescription::factory(array(
            'operations' => array(
                'works' => array(
                    'httpMethod' => 'GET',
                    'errorResponses' => array(
                        array('handling' => 'RECOVERABLE', 'class' => $mockError),
                        array('handling' => 'AGENT_ATTENTION', 'category' => 'PROCESS_FAIL',  'class' => $mockError)
                    )
                ),
                'bad_class' => array(
                    'httpMethod' => 'GET',
                    'errorResponses' => array(
                        array('handling' => 'RECOVERABLE', 'class' => 'Does\\Not\\Exist')
                    )
                ),
                'does_not_implement' => array(
                    'httpMethod' => 'GET',
                    'errorResponses' => array(
                        array('handling' => 'RECOVERABLE', 'class' => __CLASS__)
                    )
                ),
                'no_errors' => array('httpMethod' => 'GET'),
                'no_class' => array(
                    'httpMethod' => 'GET',
                    'errorResponses' => array(
                        array('handling' => 'RECOVERABLE')
                    )
                ),
            )
        ));
        $this->client = new Client($this->getServer()->getUrl());
        $this->client->setDescription($description);
    }

    /**
     * @expectedException \Otg\Ean\Plugin\EanError\Exception\EanErrorException
     */
    public function testThrowsGenericWhenErrorResponsesIsNotSet()
    {
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 70\r\n\r\n<root><EanWsError><handling>RECOVERABLE</handling></EanWsError></root>");
        $this->client->addSubscriber(new EanErrorPlugin());
        $this->client->getCommand('no_errors')->execute();
    }

    public function testSkipsWhenErrorResponsesIsNotSetAndAllowsSuccess()
    {
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 0\r\n\r\n");
        $this->client->addSubscriber(new EanErrorPlugin());
        $this->client->getCommand('no_errors')->execute();
    }

    /**
     * @expectedException \Guzzle\Plugin\ErrorResponse\Exception\ErrorResponseException
     * @expectedExceptionMessage Does\Not\Exist does not exist
     */
    public function testEnsuresErrorResponseClassExists()
    {
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 70\r\n\r\n<root><EanWsError><handling>RECOVERABLE</handling></EanWsError></root>");
        $this->client->addSubscriber(new EanErrorPlugin());
        $this->client->getCommand('bad_class')->execute();
    }

    /**
     * @expectedException \Guzzle\Plugin\ErrorResponse\Exception\ErrorResponseException
     * @expectedExceptionMessage must implement Guzzle\Plugin\ErrorResponse\ErrorResponseExceptionInterface
     */
    public function testEnsuresErrorResponseImplementsInterface()
    {
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 70\r\n\r\n<root><EanWsError><handling>RECOVERABLE</handling></EanWsError></root>");
        $this->client->addSubscriber(new EanErrorPlugin());
        $this->client->getCommand('does_not_implement')->execute();
    }

    public function testThrowsSpecificErrorResponseOnMatch()
    {
        try {
            $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 70\r\n\r\n<root><EanWsError><handling>RECOVERABLE</handling></EanWsError></root>");
            $this->client->addSubscriber(new EanErrorPlugin());
            $command = $this->client->getCommand('works');
            $command->execute();
            $this->fail('Exception not thrown');
        } catch (ErrorResponseMock $e) {
            $this->assertSame($command, $e->command);
            $this->assertEquals('RECOVERABLE', (string) $e->response->xml()->EanWsError->handling);
        }
    }

    /**
     * @expectedException \Guzzle\Tests\Mock\ErrorResponseMock
     */
    public function testThrowsWhenHandlingAndCategoryMatch()
    {
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 107\r\n\r\n<root><EanWsError><handling>AGENT_ATTENTION</handling><category>PROCESS_FAIL</category></EanWsError></root>");
        $this->client->addSubscriber(new EanErrorPlugin());
        $this->client->getCommand('works')->execute();
    }

    /**
     * @expectedException \Otg\Ean\Plugin\EanError\Exception\EanErrorException
     */
    public function testThrowsGenericWhenCategoryDoesNotMatch()
    {
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 102\r\n\r\n<root><EanWsError><handling>AGENT_ATTENTION</handling><category>UNKNOWN</category></EanWsError></root>");
        $this->client->addSubscriber(new EanErrorPlugin());
        $this->client->getCommand('works')->execute();
    }

    /**
     * @expectedException \Otg\Ean\Plugin\EanError\Exception\EanErrorException
     */
    public function testThrowsGenericWhenNoClassIsSet()
    {
        $this->getServer()->enqueue("HTTP/1.1 200 OK\r\nContent-Length: 70\r\n\r\n<root><EanWsError><handling>RECOVERABLE</handling></EanWsError></root>");
        $this->client->addSubscriber(new EanErrorPlugin());
        $this->client->getCommand('no_class')->execute();
    }
}
