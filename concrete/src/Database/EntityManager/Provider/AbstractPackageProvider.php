<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;

abstract class AbstractPackageProvider implements ProviderInterface
{

    /**
     * @var Package
     */
    protected $pkg;
    
    /**
     *
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
     * Get LegacyAnnotationReader
     * 
     * @return \Doctrine\Common\Annotations\CachedReader with a 
     *         \Doctrine\Common\Annotations\SimpleAnnotationReader
     */
    protected function getLegacyAnnotationReader()
    {
        return $this->app->make('orm/cachedSimpleAnnotationReader');
    }

    /**
     * Get StandardAnnotationReader
     * 
     * @return \Doctrine\Common\Annotations\CachedReader with a
     *         \Doctrine\Common\Annotations\AnnotationReader
     */
    protected function getStandardAnnotationReader()
    {
        return $this->app->make('orm/cachedAnnotationReader');
    }
    
    /**
     * Get the correct AnnotationReader based on the packages support for LegacyCore
     * 
     * @return \Doctrine\Common\Annotations\CachedReader
     */
    protected function getAnnotationReader()
    {
        if ($this->packageSupportsLegacyCore()) {
            $reader = $this->getLegacyAnnotationReader();
        } else {
            $reader = $this->getStandardAnnotationReader();
        }
        return $reader;
    }
    
    /**
     * Package supports legacy core
     * 
     * @return bool
     */
    protected function packageSupportsLegacyCore()
    {
        $concrete5 = '7.9.9';
        $package = $this->pkg->getApplicationVersionRequired();
        return version_compare($package, $concrete5, '<');
    }
}
