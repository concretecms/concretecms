<?php
namespace Concrete\Core\Database\EntityManager\Provider;


use Concrete\Core\Application\Application;
use Concrete\Core\Package\Package;

class PackageProviderFactory implements ProviderAggregateInterface
{

    protected $pkg;
    protected $app;

    public function __construct(Application $app, Package $pkg)
    {
        $this->app = $app;
        $this->pkg = $pkg;
    }

    public function getEntityManagerProvider()
    {
        if ($this->pkg instanceof ProviderInterface) {
            $provider = $this->pkg;
        } elseif ($this->pkg instanceof ProviderAggregateInterface) {
            $provider = $this->pkg->getEntityManagerProvider();
        } else {
            $provider = new DefaultPackageProvider($this->app, $this->pkg);
        }

        return $provider;
    }
}