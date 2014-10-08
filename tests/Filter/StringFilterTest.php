<?php

namespace Otg\Ean\Tests\Filter;

use Otg\Ean\Filter\StringFilter;

class StringFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * US dates are formatted MM/DD/YYYY
     */
    public function testFormatUsDate()
    {
        $this->assertEquals('04/30/2013', StringFilter::formatUsDate('2013-04-30'));
    }

    public function testMaskCreditCardXml()
    {
        $this->assertEquals('<creditCardType>XX</creditCardType>',
            StringFilter::maskCreditCard('<creditCardType>CA</creditCardType>'));

        $this->assertEquals('<creditCardNumber>XXXXXXXXXXXXXXXX</creditCardNumber>',
            StringFilter::maskCreditCard('<creditCardNumber>4564456445644564</creditCardNumber>'));

        $this->assertEquals('<creditCardIdentifier>XXX</creditCardIdentifier>',
            StringFilter::maskCreditCard('<creditCardIdentifier>123</creditCardIdentifier>'));

        $this->assertEquals('<creditCardExpirationMonth>XX</creditCardExpirationMonth>',
            StringFilter::maskCreditCard('<creditCardExpirationMonth>01</creditCardExpirationMonth>'));

        $this->assertEquals('<creditCardExpirationYear>XX</creditCardExpirationYear>',
            StringFilter::maskCreditCard('<creditCardExpirationYear>16</creditCardExpirationYear>'));
    }

    public function testMaskCreditCardQueryParameters()
    {
        $this->assertEquals('creditCardType=XX',
            StringFilter::maskCreditCard('creditCardType=CA'));

        $this->assertEquals('creditCardNumber=XXXXXXXXXXXXXXXX',
            StringFilter::maskCreditCard('creditCardNumber=4564456445644564'));

        $this->assertEquals('creditCardIdentifier=XXX',
            StringFilter::maskCreditCard('creditCardIdentifier=123'));

        $this->assertEquals('creditCardExpirationMonth=XX',
            StringFilter::maskCreditCard('creditCardExpirationMonth=01'));

        $this->assertEquals('creditCardExpirationYear=XX',
            StringFilter::maskCreditCard('creditCardExpirationYear=16'));
    }

    /**
     * To be safe if the phrases "credit card" and "complete trace" show up
     * in the same string the entire value is wiped
     */
    public function testMaskCreditCardStackTraceErrors()
    {
        $eanResponse1 = <<<'EOD'
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

        $eanResponse2 = <<<'EOD'
Â|INVLDÂ| CREDIT CARD TYPE NOT ACCEPTED BY PROP


Complete Trace:


0H1|1/GDPSTDC12345678901234EXP 01 16-TEST/NM-1.1/SI-75437063
REPLIED
Â|INVLDÂ| CREDIT CARD TYPE NOT ACCEPTED BY PROP
SendReceiveHostMessage :IR: =
IGD
EOD;
        $this->assertEquals('XXXX...', StringFilter::maskCreditCard($eanResponse1));
        $this->assertEquals('XXXX...', StringFilter::maskCreditCard($eanResponse2));

    }

    public function testMaskCreditCardXmlErrors()
    {
        $this->assertEquals('<creditCard>XXXX...</creditCard>',
            StringFilter::maskCreditCard('<creditCard><type>AMEX</type><number>***********4564</number><holder>John Doe</holder><expiration>02-16</expiration><securityCode>1234</securityCode></creditCard>'));

    }

    public function testMaskCreditCardXmlEncoded()
    {
        $this->assertEquals('%3CcreditCardType%3EXX%3C%2FcreditCardType%3E',
            StringFilter::maskCreditCard('%3CcreditCardType%3ECA%3C%2FcreditCardType%3E'));

        $this->assertEquals('%3CcreditCardNumber%3EXXXXXXXXXXXXXXXX%3C%2FcreditCardNumber%3E',
            StringFilter::maskCreditCard('%3CcreditCardNumber%3E4564456445644564%3C%2FcreditCardNumber%3E'));

        $this->assertEquals('%3CcreditCardIdentifier%3EXXX%3C%2FcreditCardIdentifier%3E',
            StringFilter::maskCreditCard('%3CcreditCardIdentifier%3E123%3C%2FcreditCardIdentifier%3E'));

        $this->assertEquals('%3CcreditCardExpirationMonth%3EXX%3C%2FcreditCardExpirationMonth%3E',
            StringFilter::maskCreditCard('%3CcreditCardExpirationMonth%3E01%3C%2FcreditCardExpirationMonth%3E'));

        $this->assertEquals('%3CcreditCardExpirationYear%3EXX%3C%2FcreditCardExpirationYear%3E',
            StringFilter::maskCreditCard('%3CcreditCardExpirationYear%3E16%3C%2FcreditCardExpirationYear%3E'));
    }

    public function testRemoveNewLines()
    {
        // https://en.wikipedia.org/wiki/Newline#Unicode
        $specialInformation = "\x0A,\x0B,\x0C,\x0D,\xC2\x85,\xE2\x80\xA8,\xE2\x80\xA9,";
        $this->assertEquals(',,,,,,,', StringFilter::removeNewLines($specialInformation));
    }

    /**
     * joinValues converts arrays to a comma-separated string (same as implode)
     */
    public function testCommaSeparated()
    {
        $this->assertEquals('1,2', StringFilter::joinValues(array(1,2)));
    }

    /**
     * Strings pass through joinValues without changes
     */
    public function testJoinSingleString()
    {
        $this->assertEquals('5', StringFilter::joinValues('5'));
        $this->assertEquals('5,6', StringFilter::joinValues('5,6'));
    }

    /**
     * joinValues converts ints to strings
     */
    public function testJoinSingleInt()
    {
        $this->assertEquals('5', StringFilter::joinValues(5));
    }
}
