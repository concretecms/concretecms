<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;
use Concrete\Core\Tree\Node\Type\ExpressEntryCategory;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\Group\Group;

class Version20161216000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '8.0.3';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->updateExpressFormPermissions();
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
