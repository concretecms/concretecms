<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Entity\Attribute\Key\Settings\DateTimeSettings;
use Concrete\Core\Page\Page;
use SinglePage;
use Concrete\Core\Support\Facade\Application;

class Version20170313000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // Move all stacks to the root. Putting them in a site was a mistake.
        $this->connection->executeQuery('update Pages set siteTreeID = 0 where ptID in (?, ?) or cFilename = ?', array(STACK_CATEGORY_PAGE_TYPE, STACKS_PAGE_TYPE, '/!stacks/view.php'));
    }

    public function down(Schema $schema)
    {
    }
}
