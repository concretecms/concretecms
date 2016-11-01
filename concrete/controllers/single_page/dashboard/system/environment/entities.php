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

                Config::save('concrete.cache.doctrine_dev_mode', (bool) $ddm);
                $this->redirect('/dashboard/system/environment/entities', 'entity_settings_updated');
            }
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }

    public function refresh_entities()
    {
        if ($this->token->validate("refresh_entities")) {
            if ($this->isPost()) {
                $em = ORM::entityManager();
                $config = $em->getConfiguration();

                // First, we flush the metadata cache.
                if (is_object($cache = $config->getMetadataCacheImpl())) {
                    $cache->flushAll();
                }

                // Next, we regnerate proxies
                $metadatas = $em->getMetadataFactory()->getAllMetadata();
                $em->getProxyFactory()->generateProxyClasses($metadatas, $this->app->make('config')->get('database.proxy_classes'));

                // Finally, we update the schema
                $tool = new SchemaTool($em);
                $tool->updateSchema($metadatas, true);

                $this->redirect('/dashboard/system/environment/entities', 'entities_refreshed');
            }
        }
    }

    public function entity_settings_updated()
    {
        $this->set('message', t('Doctrine development settings updated.'));
        $this->view();
    }

    public function entities_refreshed()
    {
        $this->set('message', t('Doctrine cache cleared, proxy classes regenerated, entity database table schema updated.'));
        $this->view();
    }

}
