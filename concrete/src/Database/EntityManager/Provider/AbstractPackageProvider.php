<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

abstract class AbstractPackageProvider implements ProviderInterface
{

    /**
     * @var Package
     */
    protected $pkg;
    protected $app;

    public function __construct(Package $pkg)
    {
        $this->app = $pkg->getApplication();
        $this->pkg = $pkg;
    }

    protected function getLegacyAnnotationReader()
    {
        return $this->app->make('orm/cachedSimpleAnnotationReader');
    }

    protected function getStandardAnnotationReader()
    {
        return $this->app->make('orm/cachedAnnotationReader');
    }

    protected function getAnnotationReader()
    {
        if ($this->packageSupportsLegacyCore()) {
            $reader = $this->getLegacyAnnotationReader();
        } else {
            $reader = $this->getStandardAnnotationReader();
        }
        return $reader;
    }

    protected function packageSupportsLegacyCore()
    {
        $concrete5 = '8.0.0a1';
        $package = $this->pkg->getApplicationVersionRequired();
        return version_compare($package, $concrete5, '<');
    }




}
