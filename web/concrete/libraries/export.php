<?

/**
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * A way to export concrete5 content as an xml representation.
 * @package Core
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2011 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Export {
	
	public function exportContent() {
		$xml = '<concrete5-cif version="1.0">';		
		
		// First, attribute types
		AttributeType::exportList($xml);
		
		// then block types
		BlockTypeList::exportList($xml);
		
		// now attribute keys (including user)
		AttributeKey::exportList($xml);
		
		$xml .= '</concrete5-cif>';
		return $xml;
		
	}

}