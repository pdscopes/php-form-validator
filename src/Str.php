<?php

namespace MadeSimple\Validator;

/**
 * Class Str
 *
 * @package MadeSimple\Validator
 */
class Str
{
    /**
     * Convert snake to camel.
     *
     * @param string $str
     *
     * @return string
     */
    public static function dashesToCamel($str)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
    }

    /**
     * Prettify the attribute name.
     *
     * @param string $atr
     *
     * @return string
     */
    public static function prettyAttribute($atr)
    {
        return ucfirst(str_replace(['.*', '.'], ['', ' '], $atr));
    }

    /**
     * Find the position of the Nth occurrence of a substring in a string.
     *
     * @param string $haystack
     * @param string $needle
     * @param int    $number
     *
     * @return bool|int
     */
    public static function strposX(string $haystack, string $needle, int $number = 1)
    {
        if ($number <= 0) {
            throw new \InvalidArgumentException('Number must be >= 1');
        }

        if ($number === 1) {
            return strpos($haystack, $needle);
        }

        $position = static::strposX($haystack, $needle, $number - 1);
        if ($position === false) {
            return false;
        }

        return strpos($haystack, $needle, $position + 1);
    }

    /**
     * @param string $a
     * @param string $b
     *
     * @return bool|string
     */
    public static function overlapl(string $a, string $b)
    {
        // overlapl failed if $b is an empty string
        if (empty($b)) {
            return false;
        }

        // If $a or $b are pure substrings of the other
        if (strpos($a, $b) === 0) {
            return $b;
        }
        if (strpos($b, $a) === 0) {
            return $a;
        }

        return static::overlapl($a, substr($b, 0, -1));
    }

    /**
     * Merge the overlap of pattern, field, and attribute.
     *
     * @param string $overlap    Str::overlapl of a pattern (foo.*.bar) and field (foo.*.bax)
     * @param string $attribute  Realised attribute name (foo.0.bar)
     * @param string $field      Field name (foo.*.bax)
     *
     * @return bool|string
     */
    public static function overlaplMerge($overlap, $attribute, $field)
    {
        if (($number = substr_count($overlap, '.')) === 0) {
            return false;
        }

        return substr($attribute, 0, static::strposX($attribute, '.', $number)) .
            substr($field, static::strposX($field, '.', $number));
    }
}