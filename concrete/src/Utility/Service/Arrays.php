<?php
namespace Concrete\Core\Utility\Service;

class Arrays
{
    /**
     * Fetches a value from an (multidimensional) array.
     *
     * @param array $array
     * @param string|int|array|mixed $keys a key, a list of keys, or a string in the form 'key1[key2][key3]' (see parseKeys())
     * @param mixed $default the value that is returned if key is not found
     *
     * @return mixed
     */
    public function get(array $array, $keys, $default = null)
    {
        $keys = $this->parseKeys($keys);

        if ($keys !== []) {
            $key = array_shift($keys);
            if (array_key_exists($key, $array)) {
                $value = $array[$key];
                if ($keys === []) {
                    return $value;
                }

                if (is_array($value)) {
                    return $this->get($value, $keys, $default);
                }
            }
        }

        return $default;
    }

    /**
     * Sets a value in an (multidimensional) array, creating the arrays recursivly.
     *
     * @param array $array
     * @param string|int|array|mixed $keys a key, a list of keys, or a string in the form 'key1[key2][key3]' (see parseKeys())
     * @param mixed $value
     *
     * @return array
     */
    public function set(array $array, $keys, $value)
    {
        $keys = $this->parseKeys($keys);

        if ($keys !== []) {
            $key = array_shift($keys);

            // This is the last key we've shifted
            if ($keys === []) {
                $array[$key] = $value;
            } else {
                // There are more keys so this should be an array
                if (!isset($array[$key]) || !is_array($array[$key])) {
                    $array[$key] = [];
                }
                $array[$key] = $this->set(
                    $array[$key], $keys, $value
                );
            }
        }

        return $array;
    }

    /**
     * Turns the keys into a list of keys.
     *
     * @param string|int|array|mixed $keys a key, a list of keys, or a string in the form 'key1[key2][key3]' (other values are treated as no keys)
     */
    private function parseKeys($keys): array
    {
        if (is_array($keys)) {
            return $keys;
        }
        if (is_int($keys)) {
            return [$keys];
        }
        if (!is_string($keys) || $keys === '') {
            return [];
        }
        if (strpos($keys, '[') === false) {
            return [$keys];
        }

        return explode('[', trim(str_replace(']', '', $keys), '['));
    }

    /**
     * Takes a multidimensional array and flattens it.
     *
     * @param array $array
     *
     * @return array
     */
    public function flatten(array $array)
    {
        $tmp = array();
        foreach ($array as $a) {
            if (is_array($a)) {
                $tmp = array_merge($tmp, $this->flatten($a));
            } else {
                $tmp[] = $a;
            }
        }

        return $tmp;
    }

    /**
     * Returns whether $a is a proper subset of $b.
     */
    public function subset($a, $b)
    {
        if (count(array_diff(array_merge($a, $b), $b)) == 0) {
            return true;
        } else {
            return false;
        }
    }
}
