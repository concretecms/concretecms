<?php
namespace Concrete\Block\Survey;
use Loader;
use \Concrete\Core\Legacy\DatabaseItemList;

/**
 * Returns all surveys in a site.
 *
 * @package Blocks
 * @subpackage Survey
 * @author Ryan Tyler <ryan@concrete5.org>
 * @author Andrew Embler <andrew@concrete5.org>
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class SurveyList extends DatabaseItemList {
	protected $itemsPerPage = 10;
	protected $autoSortColumns = array('cvName', 'question', 'numberOfResponses', 'lastResponse');
	
	function __construct() {
		$this->setQuery(
			   'select distinct btSurvey.bID, CollectionVersions.cID, btSurvey.question, CollectionVersions.cvName, (select max(timestamp) from btSurveyResults where btSurveyResults.bID = btSurvey.bID and btSurveyResults.cID = CollectionVersions.cID) as lastResponse, (select count(timestamp) from btSurveyResults where btSurveyResults.bID = btSurvey.bID and btSurveyResults.cID = CollectionVersions.cID) as numberOfResponses ' .
				'from btSurvey, CollectionVersions, CollectionVersionBlocks');	
		$this->filter(false, 'btSurvey.bID = CollectionVersionBlocks.bID');
		$this->filter(false, 'CollectionVersions.cID = CollectionVersionBlocks.cID');
		$this->filter(false, 'CollectionVersionBlocks.cvID = CollectionVersionBlocks.cvID');
		$this->filter(false, 'CollectionVersions.cvIsApproved = 1');
		$this->userPostQuery .= 'group by btSurvey.bID, CollectionVersions.cID';
	}
}
