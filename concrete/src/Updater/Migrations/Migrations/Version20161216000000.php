<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\User\Group\Group;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161216000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->updateExpressFormPermissions();
    }

    public function down(Schema $schema)
    {
    }

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function updateExpressFormPermissions()
    {
        $this->output(t('Adding guests to Express Form Blocks.'));

        $folder = ExpressEntryCategory::getNodeByName(ExpressFormBlockController::FORM_RESULTS_CATEGORY_NAME);
        if (is_object($folder)) {
            $folder->assignPermissions(
                Group::getByID(GUEST_GROUP_ID),
                ['add_express_entries']
            );
        }
    }
}
