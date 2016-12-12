<?php
namespace Concrete\Core\Utility\Service\Validation;
class Numbers {

    /**
     * Tests whether the passed item is an integer. Since this is frequently used by the form helper we're not checking
     * whether the TYPE of data is an integer, but whether the passed argument represents a valid text/string version of an
     * integer
     * @param $data
     * @return bool
     */
	public function integer($data) {
		if (is_int($data)) {
			return true;
		} else if (is_string($data)) {
			$iv = (string) intval($data);
			$sv = (string) $data;
			return $sv == $iv;
		}
        return false;
	}
	
}