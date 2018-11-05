<?php
namespace Concrete\Core\Database\EntityManager\Driver;

use Concrete\Core\Application\Application;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class CoreDriver implements DriverInterface
{
    /**
     * @var Application 
     */
    protected $app;
    
    /**
     * Constructor
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    /**
     * Get the AnnotationDriver
     * 
     * @return AnnotationDriver
     */
    public function getDriver()
    {
        $annotationDriver = new AnnotationDriver($this->app->make('orm/cachedAnnotationReader'), [
            DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
        ]);
        return $annotationDriver;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return 'Concrete\Core\Entity';
    }


}
