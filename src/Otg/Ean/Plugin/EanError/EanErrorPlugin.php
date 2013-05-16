<?php
// Modified from Guzzle\Plugin\ErrorResponsePlugin

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
namespace Otg\Ean\Plugin\EanError;

use Guzzle\Plugin\ErrorResponse\ErrorResponsePlugin;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\Operation;
use Guzzle\Common\Event;
use Guzzle\Plugin\ErrorResponse\Exception\ErrorResponseException;
use Otg\Ean\Plugin\EanError\Exception\EanErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EanErrorPlugin implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array('command.before_send' => array('onCommandBeforeSend', -1));
    }

    /**
     * Adds a listener to requests before they sent from a command
     *
     * @param Event $event Event emitted
     */
    public function onCommandBeforeSend(Event $event)
    {
        $command = $event['command'];
        if ($operation = $command->getOperation()) {
            $request = $command->getRequest();
            $request->getEventDispatcher()
                ->addListener('request.complete', $this->getErrorClosure($request, $command, $operation));
        }
    }

    /**
     * Matches EanWsError responses
     *
     * @param RequestInterface $request   Request that received an error
     * @param CommandInterface $command   Command that created the request
     * @param Operation        $operation Operation that defines the request and errors
     *
     * @return \Closure               Returns a closure
     * @throws ErrorResponseException
     */
    protected function getErrorClosure(RequestInterface $request, CommandInterface $command, Operation $operation)
    {
        return function (Event $event) use ($request, $command, $operation) {
            $response = $event['response'];

            $xml = $response->xml();
            if (!isset($xml->EanWsError)) {
                return;
            }

            foreach ($operation->getErrorResponses() as $error) {
                if (!isset($error['class'])) {
                    continue;
                }
                if (isset($error['handling']) && (string) $xml->EanWsError->handling != $error['handling']) {
                    continue;
                }
                if (isset($error['category']) && (string) $xml->EanWsError->category != $error['category']) {
                    continue;
                }
                $className = $error['class'];

                $errorClassInterface = 'Guzzle\\Plugin\\ErrorResponse\\ErrorResponseExceptionInterface';
                if (!class_exists($className)) {
                    throw new ErrorResponseException("{$className} does not exist");
                } elseif (!is_subclass_of($className, $errorClassInterface)) {
                    throw new ErrorResponseException("{$className} must implement {$errorClassInterface}");
                }
                throw $className::fromCommand($command, $response);
            }

            throw EanErrorException::fromCommand($command, $response);
        };
    }
}
