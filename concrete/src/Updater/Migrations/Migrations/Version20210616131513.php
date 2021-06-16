<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20210616131513 extends AbstractMigration
{

    public function upgradeDatabase()
    {
        $this->refreshBlockType('testimonial');
    }
}
