<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Http\Request;
use Concrete\Core\Package\Event\PackageEntities;
use Concrete\Core\Page\Controller\DashboardPageController;
use Doctrine\ORM\EntityManagerInterface;

class Entities extends DashboardPageController
{
    public function view()
    {
        $pev = new PackageEntities();
        $this->app->make('director')->dispatch('on_list_package_entities', $pev);
        $entityManagers = array_merge([$this->app->make(EntityManagerInterface::class)], $pev->getEntityManagers());
        $drivers = [];
        foreach ($entityManagers as $em) {
            $config = $em->getConfiguration();
            $thisDriverChain = $config->getMetadataDriverImpl();
            if ($thisDriverChain !== null) {
                $theseDrivers = $thisDriverChain->getDrivers();
                if (!empty($theseDrivers)) {
                    $drivers = array_merge($drivers, $theseDrivers);
                }
            }
        }

        $config = $this->app->make('config');
        $this->set('doctrine_dev_mode', $config->get('concrete.cache.doctrine_dev_mode') ? 'yes' : 'no');
        $this->set('drivers', $drivers);
    }

    public function update_entity_settings()
    {
        if (!$this->token->validate('update_entity_settings')) {
            $this->error->add($this->token->getErrorMessage());

            return $this->view();
        }

        if ($this->request->isMethod(Request::METHOD_POST)) {
            if ($this->request->request->get('refresh')) {
                $pev = new PackageEntities();
                $this->app->make('director')->dispatch('on_refresh_package_entities', $pev);
                $entityManagers = array_merge([$this->app->make(EntityManagerInterface::class)], $pev->getEntityManagers());
                foreach ($entityManagers as $em) {
                    $manager = new DatabaseStructureManager($em);
                    $manager->refreshEntities();
                }

                $this->flash('success', t('Doctrine cache cleared, proxy classes regenerated, entity database table schema updated.'));

                return $this->buildRedirect($this->action());
            }

            $ddm = $this->request->request->get('DOCTRINE_DEV_MODE') === 'yes';
            $this->app->make('config')->save('concrete.cache.doctrine_dev_mode', $ddm);

            $this->flash('success', t('Doctrine development settings updated.'));

            return $this->buildRedirect($this->action());
        }
    }
}
