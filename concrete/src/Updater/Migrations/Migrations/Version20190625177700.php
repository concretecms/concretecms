<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190625177700 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->fixWorkflowsPackage();
    }

    private function fixWorkflowsPackage() {

        $qb = $this->connection->createQueryBuilder();
        $qb->select('pkgID, wftID')->from('WorkflowTypes')->where($qb->expr()->gt('pkgID',0));
        $results = $qb->execute()->fetchAll();
        foreach ($results as $result) {
            $this->connection->update('Workflows', ['pkgID'=>$result['pkgID']], ['pkgID'=>0, 'wftID'=>$result['wftID']]);
        }


    }


}
