<?php
namespace Concrete\Core\Utility\Service\Validation;

use Core;

class Arrays
{
    /**
     * Returns true if any string in the "haystack" contains the "needle".
     *
     * @param string $needle The string to search for
     * @param string|array $haystack An array of strings to be searched (Can also contain sub arrays)
     * @param bool $recurse Defaults to true, when enabled this function will check any arrays inside the haystack for
     * a containing value when false it will only check the first level
     *
     * @return bool
     */
    public function containsString($needle, $haystack = array(), $recurse = true)
    {
        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringHelper */
        $stringHelper = Core::make('helper/validation/strings');
        if (!$stringHelper->notempty($needle)) {
            return false;
        }
        $arr = (!is_array($haystack)) ? array($haystack) : $haystack; // turn the string into an array
        foreach ($arr as $item) {
            if ($stringHelper->notempty($item) && strstr($item, $needle) !== false) {
                return true;
            } elseif ($recurse && is_array($item) && $this->containsString($needle, $item)) {
                return true;
            }
        }

        return false;
    }
}
