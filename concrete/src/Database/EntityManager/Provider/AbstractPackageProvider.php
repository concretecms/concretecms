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

    public function __construct(Application $app, Package $pkg)
    {
        $this->app = $app;
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




}
