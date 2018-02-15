<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20161203000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
    {
        return '8.0.1';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->fixFileFolderPermissions();
        $this->fixWorkflows();
        $this->fixTrackingCode();
    }

    protected function output($message)
    {
        $this->version->getConfiguration()->getOutputWriter()->write($message);
    }

    protected function fixFileFolderPermissions()
    {
        $this->output(t('Fixing file folder permissions'));
        $r = $this->connection->executeQuery('select fID from Files where folderTreeNodeID = 0');
        while ($row = $r->fetch()) {
            $properFolderID = $this->connection->fetchColumn(
                'select treeNodeID from TreeFileNodes where fID = ?', [$row['fID']]
            );
            if ($properFolderID) {
                $this->connection->executeQuery(
                    'update Files set folderTreeNodeID = ? where fID = ?', [$properFolderID, $row['fID']]
                );
            }
        }
    }

    protected function fixWorkflows()
    {
        $this->output(t('Updating permission keys to trigger workflows'));
        $this->connection->executeQuery(
            "update PermissionKeys set pkCanTriggerWorkflow = 1 where pkHandle in ('delete_user', 'activate_user')"
        );
    }

    protected function fixTrackingCode()
    {
        $this->output(t('Updating tracking code.'));
        $service = \Core::make('site');
        $site = $service->getDefault();
        $config = $site->getConfigRepository();
        $proceed = true;
        $tracking = $config->get('seo.tracking');
        if (is_array($tracking) && isset($tracking['header']) && $tracking['header']) {
            $proceed = false;
        }
        if (is_array($tracking) && isset($tracking['footer']) && $tracking['footer']) {
            $proceed = false;
        }
        if ($proceed) {
            // we saved it in the wrong place on the 8.0 upgrade.
            $tracking = (array) \Config::get('concrete.seo.tracking', []);
            $trackingCode = array_get($tracking, 'code');
            if (!is_array($trackingCode)) {
                array_set($tracking, 'code', ['header' => '', 'footer' => '']);
                $trackingCode = (string) $trackingCode;
                switch (array_get($tracking, 'code_position')) {
                    case 'top':
                        array_set($tracking, 'code.header', $trackingCode);
                        break;
                    case 'bottom':
                    default:
                        array_set($tracking, 'code.footer', $trackingCode);
                        break;
                }
            }
            unset($tracking['code_position']);
            $config->save('seo.tracking', $tracking);
        }
    }
}
