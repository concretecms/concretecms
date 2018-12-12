<?php
namespace Concrete\Block\Survey;

use Concrete\Core\Legacy\DatabaseItemList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Database\Connection\Connection;

class SurveyList extends DatabaseItemList
{
    protected $itemsPerPage = 10;
    protected $autoSortColumns = array('cvName', 'question', 'numberOfResponses', 'lastResponse');

    public function __construct()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $now = $app->make('date')->getOverridableNow();
        $nowSql = $db->quote($now);
        $query = <<<EOT
SELECT
    btSurvey.bID,
    CollectionVersions.cID,
    btSurvey.question,
    CollectionVersions.cvName,
    max(btSurveyResults.timestamp) AS lastResponse,
    count(btSurveyResults.bID) AS numberOfResponses
FROM
    btSurvey
    LEFT JOIN btSurveyResults
        ON btSurveyResults.bID = btSurvey.bID
    LEFT JOIN CollectionVersions
        ON CollectionVersions.cID = btSurveyResults.cID
        AND CollectionVersions.cvIsApproved = 1 AND (CollectionVersions.cvPublishDate IS NULL OR CollectionVersions.cvPublishDate <= {$nowSql}) AND (CollectionVersions.cvPublishEndDate IS NULL OR CollectionVersions.cvPublishEndDate >= {$nowSql})
EOT
        ;

        $this->setQuery($query);
        $this->userPostQuery .= 'GROUP BY btSurvey.bID, CollectionVersions.cID,
		                            btSurvey.question, CollectionVersions.cvName';
    }
}
