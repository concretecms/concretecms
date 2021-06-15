<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20210205193115 extends AbstractMigration
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('express_entry_list');
        $this->connection->executeUpdate('update btExpressEntryList set enablePagination = 1'); // make sure to do this so that existing templates don't lose pagination
    }

}
