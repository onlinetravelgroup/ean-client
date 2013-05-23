<?php
// Copyright 2013 Online Travel Group (info@onlinetravelgroup.com.au)
namespace Otg\Ean\Filter;

/**
 * Filter for manipulating strings
 *
 * @package Otg\Ean\Filter
 */
class StringFilter
{
    const US_DATE_FORMAT = 'm/d/Y';

    public static function formatUsDate($dateString)
    {
        $date = new \DateTime($dateString);

        return $date->format(self::US_DATE_FORMAT);
    }

    /**
     * Replaces credit card details in EAN messages with X's
     * @static
     * @param  string $value EAN message containing credit card details
     * @return string
     */
    public static function maskCreditCard($value)
    {
        $pattern = array(
            '~(^)(.*credit card.*complete trace.*)($)~is',
            '~(creditCardType=)([^&]+)(&|$)~',
            '~(creditCardNumber=)([^&]+)(&|$)~',
            '~(creditCardIdentifier=)([^&]+)(&|$)~',
            '~(creditCardExpirationMonth=)([^&]+)(&|$)~',
            '~(creditCardExpirationYear=)([^&]+)(&|$)~',
            '~(<creditCardType>)([^<]+)(</creditCardType>)~',
            '~(<creditCardNumber>)([^<]+)(</creditCardNumber>)~',
            '~(<creditCardIdentifier>)([^<]+)(</creditCardIdentifier>)~',
            '~(<creditCardExpirationMonth>)([^<]+)(</creditCardExpirationMonth>)~',
            '~(<creditCardExpirationYear>)([^<]+)(</creditCardExpirationYear>)~',
            '~(<creditCard>)(.*)(</creditCard>)~is',
            '~(%3CcreditCardType%3E)(.+)(%3C%2FcreditCardType%3E)~',
            '~(%3CcreditCardNumber%3E)([^<]+)(%3C%2FcreditCardNumber%3E)~',
            '~(%3CcreditCardIdentifier%3E)([^<]+)(%3C%2FcreditCardIdentifier%3E)~',
            '~(%3CcreditCardExpirationMonth%3E)([^<]+)(%3C%2FcreditCardExpirationMonth%3E)~',
            '~(%3CcreditCardExpirationYear%3E)([^<]+)(%3C%2FcreditCardExpirationYear%3E)~',
            '~(%3CcreditCard%3E)(.*)(%3C%2FcreditCard%3E)~is'
        );

        // mask details with X's
        $value = preg_replace_callback($pattern,
            function ($matches) {
                $content = '';
                if (isset($matches[2])) {
                    $len = strlen($matches[2]);

                    if ($len < 20) {
                        $content = str_repeat('X', $len);
                    } else {
                        $content = 'XXXX...';
                    }
                    $content = $matches[1] . $content . $matches[3];
                }

                return $content;
            },
            $value);

        return $value;
    }

    /**
     * Removes all Unicode new line characters in a string
     * @param $value
     * @return mixed
     */
    public static function removeNewLines($value)
    {
        return preg_replace('~\R~u', '', $value);
    }

    /**
     * Converts values to a comma-separated string
     *
     * @param  mixed  $values
     * @return string
     */
    public static function joinValues($values)
    {
        return implode(',', (array) $values);
    }
}
