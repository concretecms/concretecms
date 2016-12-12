<?php
namespace Concrete\Core\Utility\Service;

class Arrays {

	/**
	 * Fetches a value from an (multidimensional) array
	 * @param array $array
	 * @param string|int|array $keys Either one key or multiple keys
	 * @param mixedvar $default the value that is returned if key is not found
	 */
	public function get(array $array, $keys, $default = null)
	{
		$keys = $this->parseKeys($keys);

		if (is_array($array) && $keys) {
			$key = array_shift($keys);
			if (array_key_exists($key, $array)) {
				$value = $array[$key];
				if (!$keys) {
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
	 * Sets a value in an (multidimensional) array, creating the arrays recursivly
	 * @param array $array
	 * @param unknown_type $keys
	 * @param unknown_type $value
	 */
	public function set(array $array, $keys, $value)
	{
		$keys = $this->parseKeys($keys);

		if ($keys) {
			$key = array_shift($keys);

			// This is the last key we've shifted
			if (!$keys) {
				$array[$key] = $value;
			} else {
				// There are more keys so this should be an array
				if (!isset($array[$key]) || !is_array($array[$key])) {
					$array[$key] = array();
				}
				$array[$key] = $this->set(
					$array[$key], $keys, $value
				);
			}
		}
		return $array;
	}

	/**
	 * Turns the string keys into an array of keys
	 * @param string|array $keys
	 * @return array
	 */
	private function parseKeys($keys)
	{
		if (is_string($keys)) {
			if (strpos($keys, '[') !== false) {
				$keys = str_replace(']', '', $keys);
				$keys = explode('[', trim($keys, '['));
			} else {
				$keys = (array) $keys;
			}
		}
		return $keys;
	}

	/**
	 * Takes a multidimensional array and flattens it
	 * @param array $array
	 * @return array
	 */
	public function flatten(array $array) {
		$tmp = array();
		foreach($array as $a) {
			if(is_array($a)) {
				$tmp = array_merge($tmp, array_flat($a));
			} else {
				$tmp[] = $a;
			}
		}
		return $tmp;
	}

	/**
	 * Returns whether $a is a proper subset of $b
	 */
	public function subset($a, $b) {
		if (count(array_diff(array_merge($a,$b), $b)) == 0) {
	        return true;
    	} else {
			return false;
		}
	}

}
