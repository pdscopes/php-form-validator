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
     * @param string $a
     * @param string $b
     *
     * @return bool|string
     */
    public static function overlapl(string $a, string $b)
    {
        if (empty($b)) {
            return false;
        }

        if ($a === $b) {
            return $b;
        }

        if (substr_count($a, '.') > substr_count($b, '.')) {
            return static::overlapl(substr($a, 0, strrpos($a, '.')), $b);
        } else {
            return static::overlapl($a, substr($b, 0, strrpos($b, '.')));
        }
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
        $overlap   = explode('.', $overlap);
        $attribute = explode('.', $attribute);
        $field     = explode('.', $field);

        for ($i=0; $i<count($overlap); $i++) {
            $field[$i] = $attribute[$i];
        }
        return implode('.', $field);
    }
}