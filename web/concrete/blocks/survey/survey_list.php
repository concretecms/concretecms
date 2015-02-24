<?php
namespace Concrete\Block\Survey;

use \Concrete\Core\Legacy\DatabaseItemList;

class SurveyList extends DatabaseItemList
{
    protected $itemsPerPage = 10;
    protected $autoSortColumns = array('cvName', 'question', 'numberOfResponses', 'lastResponse');

    function __construct()
    {
        $query = 'SELECT btSurvey.bID, CollectionVersions.cID, btSurvey.question, CollectionVersions.cvName,
                        max(btSurveyResults.timestamp) AS lastResponse,
                        count(btSurveyResults.bID) AS numberOfResponses
                    FROM btSurvey
                    INNER JOIN CollectionVersionBlocks
                      ON btSurvey.bID = CollectionVersionBlocks.bID
                    INNER JOIN CollectionVersions
                      ON CollectionVersionBlocks.cID = CollectionVersions.cID
                        AND CollectionVersionBlocks.cvID = CollectionVersions.cvID
                        AND CollectionVersions.cvIsApproved = 1
                    LEFT JOIN btSurveyResults
                      ON btSurvey.bID = btSurveyResults.bID
                        AND btSurveyResults.cID = CollectionVersions.cID';

        $this->setQuery($query);
        $this->userPostQuery .= 'GROUP BY btSurvey.bID, CollectionVersions.cID,
		                            btSurvey.question, CollectionVersions.cvName';
    }
}
