<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\Package;

class PackageProviderFactory implements ProviderAggregateInterface
{
    
    /**
     * @var Package 
     */
    protected $pkg;
    
    /**
     * @var Application 
     */
    protected $app;
    
    /**
     * Constructor
     * 
     * @param Application $app
     * @param Package $pkg
     */
    public function __construct(Application $app, Package $pkg)
    {
        $this->app = $app;
        $this->pkg = $pkg;
    }
    
    /**
     * Get the EntityManager(Metadata)Provider
     * 
     * @return \Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider|\Concrete\Core\Package\Package
     */
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
