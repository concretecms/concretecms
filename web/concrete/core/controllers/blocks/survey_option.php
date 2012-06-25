<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Blocks
 * @subpackage BlockTypes
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object that represents a survey option.
 *
 * @package Blocks
 * @subpackage BlockTypes
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class Concrete5_Controller_Block_SurveyOption {

	public $optionID, $optionName, $displayOrder;
	
	function getOptionID() {return $this->optionID;}
	function getOptionName() {return $this->optionName;}
	function getOptionDisplayOrder() {return $this->displayOrder;}
	
	function getResults() {
		$db = Loader::db();
		$v = array($this->optionID, intval($this->cID));
		$q = "select count(resultID) from btSurveyResults where optionID = ? AND cID=?";
		$result = $db->getOne($q, $v);
		if ($result > 0) {
			return $result;
		} else {
			return 0;
		}
	}
}