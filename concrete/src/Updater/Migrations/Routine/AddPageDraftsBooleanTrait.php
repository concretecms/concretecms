<?php

namespace Concrete\Core\Updater\Migrations\Routine;

use Concrete\Core\Support\Facade\Facade;
use Doctrine\DBAL\Schema\Schema;

trait AddPageDraftsBooleanTrait
{
    public function addColumnIfMissing(Schema $schema)
    {
        if (!$schema->getTable('Pages')->hasColumn('cIsDraft')) {
            $schema->getTable('Pages')->addColumn('cIsDraft', 'boolean', [
                'notnull' => true, 'default' => 0,
            ]);
        }
    }

    public function migrateDrafts()
    {
        $app = Facade::getFacadeApplication();
        $db = $app->make('database')->connection();
        $config = $app->make('config');
        $r = $db->executeQuery('select cID from PagePaths where cPath = ?', [$config->get('concrete.paths.drafts')]);
        while ($row = $r->fetch()) {
            $r2 = $db->executeQuery('select cID from Pages where cParentID = ?', [$row['cID']]);
            while ($row2 = $r2->fetch()) {
                $db->update('Pages', ['cIsDraft' => 1], ['cID' => $row2['cID']]);
            }
        }
    }
}
