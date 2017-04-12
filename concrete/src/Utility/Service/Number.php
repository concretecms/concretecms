<?php
namespace Concrete\Core\Utility\Service;

class Number
{
    /**
     * Rounds the value only out to its most significant digit.
     *
     * @param string $value
     *
     * @return float|null
     */
    public function flexround($value)
    {
        return ($value === null || $value === '') ? null : (float) $value;
    }

    /**
     * Remove superfluous zeroes from a string containing a number.
     *
     * @param string $value (decimal separator is dot)
     *
     * @return string
     */
    public function trim($value)
    {
        $result = '';
        $value = (string) $value;
        if ($value !== '') {
            // Temporarily remove leadin sign
            $sign = $value[0];
            if ($sign === '-' || $sign === '+') {
                $value = substr($value, 1);
            } else {
                $sign = '';
            }
            if ($value !== '') {
                // Remove initial zeroes
                $value = ltrim($value, '0');
                if ($value === '' || $value[0] === '.') {
                    $value = '0' . $value;
                }
                if (strpos($value, '.') !== false) {
                    // Remove trailing zeroes after the dot
                    $value = rtrim(rtrim($value, '0'), '.');
                }
                $result = $sign . $value;
            }
        }

        return $result;
    }

    /**
     * Checks if a given string is valid representation of a number in the current locale.
     *
     * @return bool
     *
     * @example http://www.concrete5.org/documentation/how-tos/developers/formatting-numbers/ See the Formatting numbers how-to for more details
     */
    public function isNumber($string)
    {
        return \Punic\Number::isNumeric($string);
    }

    /**
     * Checks if a given string is valid representation of an integer in the current locale.
     *
     * @return bool
     *
     * @example http://www.concrete5.org/documentation/how-tos/developers/formatting-numbers/ See the Formatting numbers how-to for more details
     */
    public function isInteger($string)
    {
        return \Punic\Number::isInteger($string);
    }

    /**
     * Format a number with grouped thousands and localized decimal point/thousands separator.
     *
     * @param number $number The number being formatted
     * @param int|null $precision [default: null] The wanted precision; if null or not specified the complete localized number will be returned
     *
     * @return string
     *
     * @example http://www.concrete5.org/documentation/how-tos/developers/formatting-numbers/ See the Formatting numbers how-to for more details
     */
    public function format($number, $precision = null)
    {
        return \Punic\Number::format($number, $precision);
    }

    /**
     * Parses a localized number representation and returns the number (or null if $string is not a valid number representation).
     *
     * @param string $string The number representation to parse
     * @param bool $trim [default: true] Remove spaces and new lines at the start/end of $string?
     * @param int|null $precision [default: null] The wanted precision; if null or not specified the complete number will be returned
     *
     * @return null|number
     *
     * @example http://www.concrete5.org/documentation/how-tos/developers/formatting-numbers/ See the Formatting numbers how-to for more details
     */
    public function unformat($string, $trim = true, $precision = null)
    {
        $result = null;
        $number = \Punic\Number::unformat($string);
        if (!is_null($number)) {
            if (is_numeric($precision)) {
                $number = round($number, $precision);
            }
            $result = $number;
        }

        return $result;
    }

    /**
     * Formats a size (measured in bytes, KB, MB, ...).
     *
     * @param number $size The size to be formatted, in bytes
     * @param string $forceUnit = '' Set to 'bytes', 'KB', 'MB', 'GB' or 'TB' if you want to force the unit, leave empty to automatically determine the unit
     *
     * @return string|mixed If $size is not numeric, the function returns $size (untouched), otherwise it returns the size with the correct usits (GB, MB, ...) and formatted following the locale rules
     *
     * @example formatSize(0) returns '0 bytes'
     * @example formatSize(1) returns '1 byte'
     * @example formatSize(1000) returns '1,000 bytes'
     * @example formatSize(1024) returns '1.00 KB'
     * @example formatSize(1024, 'bytes') returns '1024 bytes'
     * @example formatSize(1024, 'GB') returns '0.00 GB'
     * @example formatSize(2000000) returns '1.91 MB'
     * @example formatSize(-5000) returns '-4.88 KB'
     * @example formatSize('hello') returns 'hello'
     */
    public function formatSize($size, $forceUnit = '')
    {
        if (!is_numeric($size)) {
            return $size;
        }
        if (strlen($forceUnit) && array_search($forceUnit, ['bytes', 'KB', 'MB', 'GB', 'TB']) === false) {
            $forceUnit = '';
        }
        if ($forceUnit === 'bytes' || (abs($size) < 1024 && (!strlen($forceUnit)))) {
            return t2(/*i18n %s is a number */'%s byte', '%s bytes', $size, $this->format($size, 0));
        }
        $size /= 1024;
        if ($forceUnit === 'KB' || (abs($size) < 1024 && (!strlen($forceUnit)))) {
            return t(/*i18n %s is a number, KB means Kilobyte */'%s KB', $this->format($size, 2));
        }
        $size /= 1024;
        if ($forceUnit === 'MB' || (abs($size) < 1024 && (!strlen($forceUnit)))) {
            return t(/*i18n %s is a number, MB means Megabyte */'%s MB', $this->format($size, 2));
        }
        $size /= 1024;
        if ($forceUnit === 'GB' || (abs($size) < 1024 && (!strlen($forceUnit)))) {
            return t(/*i18n %s is a number, GB means Gigabyte */'%s GB', $this->format($size, 2));
        }

        return t(/*i18n %s is a number, TB means Terabyte */'%s TB', $this->format($size, 2));
    }

    /**
     * Nice and elegant function for converting memory. Thanks to @lightness races in orbit on Stackoverflow.
     *
     * @param $val
     *
     * @return int|string
     */
    public function getBytes($val)
    {
        $val = trim($val);
        if ($val !== '') {
            $last = strtolower($val[strlen($val) - 1]);
            if (!is_numeric($last)) {
                $num = trim(substr($val, 0, -1));
                switch ($last) {
                    case 'g':
                        $num *= 1024;
                    case 'm':
                        $num *= 1024;
                    case 'k':
                        $num *= 1024;
                        $val = $num;
                        break;
                }
            }
        }

        return $val;
    }
}
