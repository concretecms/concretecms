<?php
namespace Concrete\Block\Survey;
use Loader;
/**
 * An object that represents an option in a survey. 
 *
 * @package Blocks
 * @subpackage Survey
 * @author Ryan Tyler <ryan@concrete5.org>
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Option {

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