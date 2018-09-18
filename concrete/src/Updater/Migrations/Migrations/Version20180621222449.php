<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180621222449 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $db = $this->connection;
        if ($db->tableExists('atPageSelector')) {
            // This is the name of the page selector attribute table in some implementations of the page selector attribute
            // We need to take this data and place it into atNumber.
            $db->query(<<<EOT
insert into atNumber (avID, value)
    select
        atPageSelector.avID, atPageSelector.value
    from
        atPageSelector
    inner join
        AttributeValues on atPageSelector.avID = AttributeValues.avID
    left join
        atNumber on atPageSelector.avID = atNumber.avID
    where
        atNumber.avID is null
EOT
            );
        }
    }
}
