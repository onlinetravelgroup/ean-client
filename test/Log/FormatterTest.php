<?php

namespace Otg\Ean\Tests\Log;

use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Otg\Ean\Log\Formatter;

class FormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testEnsuresRequestIsMasked()
    {
        $formatter = new Formatter('{request}');

        $request = new Request('POST', 'http://foo.com?q=test');
        $request->setBody(Stream::factory('<xml><creditCardType>CA</creditCardType>' .
            '<creditCardNumber>4564456445644564</creditCardNumber>' .
            '<creditCardIdentifier>123</creditCardIdentifier>' .
            '<creditCardExpirationMonth>01</creditCardExpirationMonth>' .
            '<creditCardExpirationYear>16</creditCardExpirationYear></xml>'));

        $this->assertEquals("POST /?q=test HTTP/1.1\r\n".
            "Host: foo.com\r\n\r\n".

            "<xml><creditCardType>XX</creditCardType>" .
            "<creditCardNumber>XXXXXXXXXXXXXXXX</creditCardNumber>" .
            "<creditCardIdentifier>XXX</creditCardIdentifier>" .
            "<creditCardExpirationMonth>XX</creditCardExpirationMonth>" .
            "<creditCardExpirationYear>XX</creditCardExpirationYear></xml>", $formatter->format($request));
    }

    public function testEnsuresRequestBodyIsMasked()
    {
        $formatter = new Formatter('{req_body}');

        $request = new Request('POST', 'http://foo.com?q=test');
        $request->setBody(Stream::factory('<xml><creditCardType>CA</creditCardType>' .
            '<creditCardNumber>4564456445644564</creditCardNumber>' .
            '<creditCardIdentifier>123</creditCardIdentifier>' .
            '<creditCardExpirationMonth>01</creditCardExpirationMonth>' .
            '<creditCardExpirationYear>16</creditCardExpirationYear></xml>'));

        $this->assertEquals('<xml><creditCardType>XX</creditCardType>' .
            '<creditCardNumber>XXXXXXXXXXXXXXXX</creditCardNumber>' .
            '<creditCardIdentifier>XXX</creditCardIdentifier>' .
            '<creditCardExpirationMonth>XX</creditCardExpirationMonth>' .
            '<creditCardExpirationYear>XX</creditCardExpirationYear></xml>', $formatter->format($request));
    }

    public function testEnsuresReqUrlIsMasked()
    {
        $formatter = new Formatter('{url}');

        $request = new Request('POST',
            'http://foo.com?creditCardType=CA' .
            '&creditCardNumber=4564456445644564' .
            '&creditCardIdentifier=123' .
            '&creditCardExpirationMonth=01' .
            '&creditCardExpirationYear=16');

        $this->assertEquals('http://foo.com?creditCardType=XX' .
            '&creditCardNumber=XXXXXXXXXXXXXXXX' .
            '&creditCardIdentifier=XXX' .
            '&creditCardExpirationMonth=XX' .
            '&creditCardExpirationYear=XX', $formatter->format($request));
    }

    public function testEnsuresResourceIsMasked()
    {
        $formatter = new Formatter('{resource}');

        $request = new Request('POST',
            'http://foo.com?creditCardType=CA' .
            '&creditCardNumber=4564456445644564' .
            '&creditCardIdentifier=123' .
            '&creditCardExpirationMonth=01' .
            '&creditCardExpirationYear=16');

        $this->assertEquals('/?creditCardType=XX' .
            '&creditCardNumber=XXXXXXXXXXXXXXXX' .
            '&creditCardIdentifier=XXX' .
            '&creditCardExpirationMonth=XX' .
            '&creditCardExpirationYear=XX', $formatter->format($request));
    }

    public function testEnsuresResponseBodyIsMasked()
    {
        $formatter = new Formatter('{url} - {res_body}');

        $request = new Request('POST', 'http://foo.com');

        $body = <<<'EOD'
                CREDIT CARD INFORMATION IS NOT VALID


Complete Trace:


0H1|7/GVI0123456789012345EXP 01 16-TEST/NM-1.1/SI-67532127
REPLIED
 1 HHL BW SS1 RZE IN21SEP-OUT24SEP   3NT  111712 BEST WESTERN F
ERDYN 1B1KSENC-2/RR63.00EUR/AGT26512673/GVI0123456789012345EXP
01 16-TEST/NM-TEST TEST/C24H/SI-67532127-CF- / ADDRESS REQUIRED
 ** PLEASE ADVISE CLIENT OF EUR RATE **
DIRECT CONNECT REQUEST PENDINGDIRECT CONNECT RESPONSE RECEIVED
 1  HHL BW NN1 RZE IN21SEP W-OUT24SEP   3NT  111712 BEST WESTER
N FERDYN 1B1KSENC-2/RR63.00EUR/AGT26512673/GVIXXXXXXXXXXXX0019E
XP 01 16-TEST/NM-TEST TEST/C24H/SI-67532127-CF-
CREDIT CARD INFORMATION IS NOT VALID
CLEAN UP TA Command
REPLIED

SendReceiveHostMessage :IR: =
IGD
EOD;

        $response = new Response(200, [], Stream::factory($body));

        $this->assertEquals('http://foo.com - XXXX...', $formatter->format($request, $response));
    }

    public function testEnsuresResponseIsMasked()
    {
        $formatter = new Formatter('{url} - {response}');

        $request = new Request('POST', 'http://foo.com');

        $body = <<<'EOD'
                CREDIT CARD INFORMATION IS NOT VALID


Complete Trace:


0H1|7/GVI0123456789012345EXP 01 16-TEST/NM-1.1/SI-67532127
REPLIED
 1 HHL BW SS1 RZE IN21SEP-OUT24SEP   3NT  111712 BEST WESTERN F
ERDYN 1B1KSENC-2/RR63.00EUR/AGT26512673/GVI0123456789012345EXP
01 16-TEST/NM-TEST TEST/C24H/SI-67532127-CF- / ADDRESS REQUIRED
 ** PLEASE ADVISE CLIENT OF EUR RATE **
DIRECT CONNECT REQUEST PENDINGDIRECT CONNECT RESPONSE RECEIVED
 1  HHL BW NN1 RZE IN21SEP W-OUT24SEP   3NT  111712 BEST WESTER
N FERDYN 1B1KSENC-2/RR63.00EUR/AGT26512673/GVIXXXXXXXXXXXX0019E
XP 01 16-TEST/NM-TEST TEST/C24H/SI-67532127-CF-
CREDIT CARD INFORMATION IS NOT VALID
CLEAN UP TA Command
REPLIED

SendReceiveHostMessage :IR: =
IGD
EOD;

        $response = new Response(200, [], Stream::factory($body));

        $this->assertEquals('http://foo.com - XXXX...', $formatter->format($request, $response));
    }
}
