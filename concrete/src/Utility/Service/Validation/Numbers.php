<?php
namespace Concrete\Core\Utility\Service\Validation;

class Numbers
{
    /**
     * Tests whether the passed item is an integer.
     * Since this is frequently used by the form helper we're not checking whether the TYPE of data is an integer,
     * but whether the passed argument represents a valid text/string version of an integer.
     *
     * @param mixed $data
     * @param int|null $min the minimum acceptable value of the integer (pass NULL to not check the minimum value)
     * @param int|null $max the maximum acceptable value of the integer (pass NULL to not check the maximum value)
     *
     * @return bool
     */
    public function integer($data, $min = null, $max = null)
    {
        if (is_int($data)) {
            $result = true;
        } elseif (is_string($data)) {
            $iv = (string) (int) $data;
            $result = $data === $iv;
        } else {
            $result = false;
        }
        if ($result === true && $min !== null && (int) $data < (int) $min) {
            $result = false;
        }
        if ($result === true && $max !== null && (int) $data > (int) $max) {
            $result = false;
        }

        return $result;
    }

    /**
     * Tests whether the passed item is an integer or a floating point number.
     * Since this is frequently used by the form helper we're not checking whether the TYPE of data is an integer or a float,
     * but whether the passed argument represents a valid text/string version of an integer or a float.
     *
     * @param mixed $data
     * @param int|float|null $min the minimum acceptable value of the number (pass NULL to not check the minimum value)
     * @param int|float|null $max the maximum acceptable value of the number (pass NULL to not check the maximum value)
     *
     * @return bool
     */
    public function number($data, $min = null, $max = null)
    {
        switch (gettype($data)) {
            case 'integer':
            case 'double':
                $result = true;
                break;
            case 'string':
                $result = preg_match('/^-?(\d+(\.\d*)?|\.\d+)$/', $data) ? true : false;
                break;
            default:
                $result = false;
                break;
        }
        if ($result === true && $min !== null && (float) $data < (float) $min) {
            $result = false;
        }
        if ($result === true && $max !== null && (float) $data > (float) $max) {
            $result = false;
        }

        return $result;
    }
}
