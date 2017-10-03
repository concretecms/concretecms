<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Geolocator\GeolocatorService;
use Concrete\Core\Page\Controller\DashboardPageController;

class Geolocation extends DashboardPageController
{
    public function on_start()
    {
        parent::on_start();
        $this->addHeaderItem(<<<EOT
<style>
table.geolocation-libraries tr[data-editurl] {
    cursor: pointer;
}
</style>
EOT
        );
    }

    public function view()
    {
        $this->set('geolocators', $this->app->make(GeolocatorService::class)->getList());
    }

    public function details($id)
    {
        $service = $this->app->make(GeolocatorService::class);
        $geolocator = $service->getByID($id);
        if ($geolocator === null) {
            $this->error->add(t('Unable to find the geolocator library specified.'));
            $this->view();
        } else {
            $this->set('pageTitle', $geolocator->getGeolocatorDisplayName());
            $this->set('service', $service);
            $this->set('geolocator', $geolocator);
            $this->set('geolocatorController', $service->getController($geolocator));
        }
    }

    public function configure($id)
    {
        if (!$this->token->validate('ccm-geolocator-configure')) {
            $this->error->add($this->token->getErrorMessage());
            $this->details($id);
        } else {
            $service = $this->app->make(GeolocatorService::class);
            $geolocator = $service->getByID($id);
            if ($geolocator === null) {
                $this->error->add(t('Unable to find the geolocator library specified.'));
                $this->view();
            } else {
                $controller = $service->getController($geolocator);
                $configuration = $controller->saveConfigurationForm($geolocator->getGeolocatorConfiguration(), $this->request->request, $this->error);
                if ($this->error->has()) {
                    $this->details($id);
                } else {
                    $geolocator->setGeolocatorConfiguration($configuration);
                    if ($this->request->request->get('geolocator-active')) {
                        $service->setCurrent($geolocator);
                    } elseif ($geolocator->isActive()) {
                        $service->setCurrent(null);
                    }
                    $service->getEntityManager()->flush($geolocator);
                    $this->flash('success', t('Geolocator library updated successfully'));
                    $this->redirect($this->action(''));
                }
            }
        }
    }
}
