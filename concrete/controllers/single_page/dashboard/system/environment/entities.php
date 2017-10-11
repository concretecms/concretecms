<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Package\CustomEntityManagersInterface;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Doctrine\ORM\EntityManagerInterface;

class Entities extends DashboardPageController
{
    public function view()
    {
        // Retrieve all entity manager drivers and show data about them
        $drivers = [];
        foreach ($this->getAllEntityManagers() as $em) {
            $config = $em->getConfiguration();
            $thisDriverChain = $config->getMetadataDriverImpl();
            if ($thisDriverChain !== null) {
                $theseDrivers = $thisDriverChain->getDrivers();
                if (!empty($theseDrivers)) {
                    $drivers = array_merge($drivers, $theseDrivers);
                }
            }
        }
        $this->set('doctrine_dev_mode', (bool) $this->app->make('config')->get('concrete.cache.doctrine_dev_mode'));
        $this->set('drivers', $drivers);
    }

    public function update_entity_settings()
    {
        if (!$this->token->validate('update_entity_settings')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            if ($this->isPost()) {
                $ddm = $this->post('DOCTRINE_DEV_MODE') === 'yes';

                if ($this->request->request->get('refresh')) {
                    foreach ($this->getAllEntityManagers() as $em) {
                        $manager = new DatabaseStructureManager($em);
                        $manager->refreshEntities();
                    }

                    $this->flash('success', t('Doctrine cache cleared, proxy classes regenerated, entity database table schema updated.'));
                    $this->redirect('/dashboard/system/environment/entities', 'view');
                } else {
                    $this->app->make('config')->save('concrete.cache.doctrine_dev_mode', $ddm);
                    $this->flash('success', t('Doctrine development settings updated.'));
                }
                $this->redirect('/dashboard/system/environment/entities', 'view');
            }
        } else {
            $this->set('error', [$this->token->getErrorMessage()]);
        }
    }

    /**
     * @return EntityManagerInterface[]
     */
    private function getAllEntityManagers()
    {
        $result = [$this->app->make(EntityManagerInterface::class)];
        $packageService = $this->app->make(PackageService::class);
        foreach ($packageService->getInstalledHandles() as $packageHandle) {
            $package = $packageService->getClass($packageHandle);
            if ($package instanceof CustomEntityManagersInterface) {
                foreach ($package->getCustomPackageEntityManagers() as $em) {
                    if (!in_array($em, $result, true)) {
                        $result[] = $em;
                    }
                }
            }
        }

        return $result;
    }
}
