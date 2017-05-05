<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170201000000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $config = $this->getConfig();

        // In case public registrations have been enabled with manual approval,
        // the user needs to setup workflows manually to apply the same
        // functionality after the upgrade. It is safer to disable public
        // registrations for as long as it takes to setup the workflows.
        if ($config->get('concrete.user.registration.enabled') &&
            $config->get('concrete.user.registration.approval')
        ) {
            $config->save('concrete.user.registration.enabled', false);
            $config->save('concrete.user.registration.approval', null);
            $config->save('concrete.user.registration.type', 'disabled');
        }
    }

    public function down(Schema $schema)
    {
        $config = $this->getConfig();

        // In case reverting the changes, it is also safer to assume that
        // the user might want to setup the manual approval process and
        // therefore disable the public registrations until it has been done.
        if ($config->get('concrete.user.registration.enabled')) {
            $config->save('concrete.user.registration.enabled', false);
            $config->save('concrete.user.registration.type', 'disabled');
        }
    }

    /**
     * @return Repository
     */
    protected function getConfig()
    {
        $app = Application::getFacadeApplication();
        return $app->make(Repository::class);
    }
}
