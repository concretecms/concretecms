<?php
namespace Concrete\Core\Database\EntityManager\Driver;

use Concrete\Core\Application\Application;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class CoreDriver implements DriverInterface
{

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getDriver()
    {
        $annotationDriver = new AnnotationDriver($this->app->make('orm/cachedAnnotationReader'), [
            DIR_BASE_CORE.DIRECTORY_SEPARATOR.DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
        ]);
        return $annotationDriver;
    }

    public function getNamespace()
    {
        return 'Concrete\Core\Entity';
    }


}
