<?php
namespace Concrete\Core\Service\Manager;

use Concrete\Core\Application\Application;

class ServiceManagerServiceProvider extends \Concrete\Core\Foundation\Service\Provider
{
    public function register()
    {
        $this->app->singleton('Concrete\Core\Service\Manager\ServiceManager', function (Application $app) {
            // Build an instance of the manager
            $manager = $app->build('Concrete\Core\Service\Manager\ServiceManager');
            /* @var ManagerInterface $manager */

            // Load the services configuration
            $services = $app['config']->get('services');

            // Loop through the configured services and extend the manager
            foreach ($services as $handle => $configuration) {
                $configuration = (array) $configuration;
                $class = array_shift($configuration);

                if ($configuration) {
                    $abstract = function ($app) use ($class, $configuration) {
                        return $app->make($class, $configuration);
                    };
                } else {
                    $abstract = $class;
                }

                $manager->extend($handle, $abstract);
            }

            return $manager;
        });
        $this->app->singleton('Concrete\Core\Service\Manager\ManagerInterface', 'Concrete\Core\Service\Manager\ServiceManager');
    }
}
