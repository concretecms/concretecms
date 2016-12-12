<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Package\Package;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Config;
use ORM;

class Entities extends DashboardPageController
{

    public function view()
    {
    }

    public function update_entity_settings()
    {
        if ($this->token->validate("update_entity_settings")) {
            if ($this->isPost()) {
                $ddm = $this->post('DOCTRINE_DEV_MODE') == 1 ? 1 : 0;

                Config::save('concrete.cache.doctrine_dev_mode', !!$ddm);
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
                if (is_object($cache = $config->getMetadataCacheImpl())) {
                    $cache->flushAll();
                }
                try {
                    $packages = Package::getInstalledList();
                    foreach($packages as $package) {
                        $package->installEntitiesDatabase();
                    }

                    $dbm = Core::make('database/structure', array($em));
                    $dbm->destroyProxyClasses('ApplicationSrc');
                    if ($dbm->hasEntities()) {
                        $dbm->generateProxyClasses();
                        $dbm->installDatabase();
                    }
                    $this->redirect('/dashboard/system/environment/entities', 'entities_refreshed');

                } catch (\Doctrine\Common\Persistence\Mapping\MappingException $e) {
                    $drv = $em->getConfiguration()->getMetadataDriverImpl();
                    $this->error->add(t("The application specific entities directory is missing. Please create it first at: %s.", array_shift($drv->getPaths())));
                } catch (\Exception $e) {
                    $this->error->add($e->getMessage());
                }
            }
        }
    }

    public function entity_settings_updated()
    {
        $this->set('message', t('Database entities configurations saved.'));
        $this->view();
    }

    public function entities_refreshed()
    {
        $this->set('message', t('Application specific database entities were refreshed.'));
        $this->view();
    }

}
