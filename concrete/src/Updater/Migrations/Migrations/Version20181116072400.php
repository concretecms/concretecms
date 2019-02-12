<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use PDO;

class Version20181116072400 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->ensureCollectionVersionPublicDates();
    }

    protected function ensureCollectionVersionPublicDates()
    {
        $this->fixPublishStartEndDates();
        $this->fixOverlappingStartEndDates();
    }

    /**
     * Be sure that cvPublishDate is not greater than cvPublishEndDate.
     */
    protected function fixPublishStartEndDates()
    {
        $this->connection->executeQuery(
            <<<EOT
UPDATE CollectionVersions
SET
    cvPublishDate = (@publishDate:=cvPublishDate),
    cvPublishDate = cvPublishEndDate,
    cvPublishEndDate = @publishDate
WHERE
    cvPublishDate IS NOT NULL
    AND cvPublishEndDate IS NOT NULL
    AND cvPublishDate > cvPublishEndDate
EOT
        );
    }

    /**
     * Be sure that there are not approved collection versions with overlapping publish dates.
     */
    protected function fixOverlappingStartEndDates()
    {
        $rs = $this->connection->executeQuery(<<<'EOT'
select
    cv1.cID,
    cv1.cvID
from
    CollectionVersions as cv1
    inner join CollectionVersions as cv2
        on cv1.cID = cv2.cID
        and cv1.cvID <> cv2.cvID
        and cv1.cvIsApproved = cv2.cvIsApproved
where
    cv1.cvIsApproved = 1
    and ifnull(cv1.cvPublishDate, '0000-00-00 00:00:00') <= ifnull(cv1.cvPublishEndDate, '9999-99-99 99:99:99')
    and ifnull(cv1.cvPublishEndDate, '9999-99-99 99:99:99') >= ifnull(cv2.cvPublishDate, '0000-00-00 00:00:00')
order by
    cv1.cID,
    cv1.cvID desc
EOT
        );
        $page = null;
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            if ($page === null || $page->getCollectionID() != $row['cID']) {
                $page = Page::getByID($row['cID'], $row['cvID']);
            } else {
                $page->loadVersionObject($row['cvID']);
            }
            $version = $page->getVersionObject();
            // Force fixing other collection version publish date/time interval
            $version->setPublishInterval($version->getPublishDate(), $version->getPublishDate());
        }
    }
}
