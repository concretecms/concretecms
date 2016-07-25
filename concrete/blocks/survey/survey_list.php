<?php
namespace Concrete\Block\Survey;

use Concrete\Core\Legacy\DatabaseItemList;

class SurveyList extends DatabaseItemList
{
    protected $itemsPerPage = 10;
    protected $autoSortColumns = array('cvName', 'question', 'numberOfResponses', 'lastResponse');

    public function __construct()
    {
        $query = 'SELECT btSurvey.bID, CollectionVersions.cID, btSurvey.question, CollectionVersions.cvName,
                        max(btSurveyResults.timestamp) AS lastResponse,
                        count(btSurveyResults.bID) AS numberOfResponses
                    FROM btSurvey
                    LEFT JOIN btSurveyResults ON btSurveyResults.bID = btSurvey.bID
                    LEFT JOIN CollectionVersions ON CollectionVersions.cID = btSurveyResults.cID AND CollectionVersions.cvIsApproved = 1';

        $this->setQuery($query);
        $this->userPostQuery .= 'GROUP BY btSurvey.bID, CollectionVersions.cID,
		                            btSurvey.question, CollectionVersions.cvName';
    }
}
