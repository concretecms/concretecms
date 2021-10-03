<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Site\Type\Service;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20210623000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * Set default site type ID for page types created before 8.0.
     *
     * @see https://github.com/concrete5/concrete5/issues/9770
     */
    public function upgradeDatabase()
    {
        $siteTypeService = $this->app->make(Service::class);
        $defaultSiteType = $siteTypeService->getDefault();
        if ($defaultSiteType) {
            $qb = $this->connection->createQueryBuilder();
            $qb->update('PageTypes')
                ->set('siteTypeID', $defaultSiteType->getSiteTypeID())
                ->where($qb->expr()->orX(
                    $qb->expr()->isNull('siteTypeID'),
                    $qb->expr()->eq('siteTypeID', 0)
                ))
                ->execute();
        }
    }
}