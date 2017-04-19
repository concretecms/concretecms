<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Config;
use Doctrine\ORM\Tools\SchemaTool;
use ORM;

class Entities extends DashboardPageController
{
    public function view()
    {
        // Retrieve all entity manager drivers and show data about them
        $config = $this->getEntityManager()->getConfiguration();
        $driverChain = $config->getMetadataDriverImpl();
        $drivers = $driverChain->getDrivers();
        $this->set('drivers', $drivers);
    }


    public function update_entity_settings()
    {
        if (!$this->token->validate("update_entity_settings")) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            if ($this->isPost()) {
                $ddm = $this->post('DOCTRINE_DEV_MODE') == 1 ? 1 : 0;

                if ($this->request->request->get('refresh')) {
                    $em = ORM::entityManager();
                    $manager = new DatabaseStructureManager($em);
                    $manager->refreshEntities();

                    $this->flash('success', t('Doctrine cache cleared, proxy classes regenerated, entity database table schema updated.'));
                    $this->redirect('/dashboard/system/environment/entities', 'view');
                } else {
                    Config::save('concrete.cache.doctrine_dev_mode', (bool) $ddm);
                    $this->flash('success', t('Doctrine development settings updated.'));
                }
                $this->redirect('/dashboard/system/environment/entities', 'view');
            }
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }
}
