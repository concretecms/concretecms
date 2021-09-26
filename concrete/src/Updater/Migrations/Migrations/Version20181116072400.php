<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Error\UserMessageException;
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
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select('p.cID')
            ->from('Pages', 'p')
            ->innerJoin('p', 'Collections', 'c', 'p.cID = c.cID')
            ->innerJoin('p', 'CollectionVersions', 'cv', 'p.cID = cv.cID')
            ->andWhere('p.cPointerID < 1')
            ->andWhere('p.cIsTemplate = 0')
            ->andWhere('cv.cvID = (select cvID from CollectionVersions where cID = cv.cID and cvIsApproved = 1 and (cvPublishDate is not null or cvPublishEndDate is not null) order by cvPublishDate desc limit 1)')
            ->execute()
        ;

        while ($row = $result->fetch()) {
            $page = Page::getByID($row['cID'], 'SCHEDULED');
            $version = $page->getVersionObject();
            try {
                // Force fixing other collection version publish date/time interval
                $version->setPublishInterval($version->getPublishDate(), $version->getPublishEndDate());
            } catch (UserMessageException $e) {
            }
        }
    }
}
