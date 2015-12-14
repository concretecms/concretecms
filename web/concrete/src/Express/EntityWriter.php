<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use \Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Exception\InvalidClassLocationDefinedException;
use Concrete\Core\Express\Exception\NoNamespaceDefinedException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\Setup;

class EntityWriter
{

    protected $application;
    protected $namespace;
    protected $outputPath;
    protected $rootEntityManager;

    public function __construct(EntityManager $entityManager, Application $application)
    {
        $this->application = $application;
        $this->rootEntityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->rootEntityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->rootEntityManager = $entityManager;
    }


    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getOutputPath()
    {
        return $this->outputPath;
    }

    /**
     * @param mixed $outputPath
     */
    public function setOutputPath($outputPath)
    {
        $this->outputPath = $outputPath;
    }

    protected function getClassName(Entity $entity)
    {
        return $this->namespace . '\\' . $entity->getName();
    }

    protected function ensureClassLocation()
    {
        if (!$this->outputPath) {
            throw new InvalidClassLocationDefinedException();
        }

        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0777, true);
        }

        if (!is_dir($this->outputPath) || !is_writable($this->outputPath)) {
            throw new InvalidClassLocationDefinedException();
        }

    }

    public function writeClass(Entity $entity)
    {

        if (!$this->namespace) {
            throw new NoNamespaceDefinedException();
        }

        $this->ensureClassLocation();

        $generator = new EntityGenerator();
        $generator->setGenerateAnnotations(true);
        $generator->setGenerateStubMethods(true);
        $generator->setAnnotationPrefix(null);
        $generator->setRegenerateEntityIfExists(true);
        $generator->setClassToExtend('\Concrete\Core\Express\BaseEntity');
        $generator->setUpdateEntityIfExists(true);
        $generator->setNumSpaces(4);
        $generator->setBackupExisting(false);

        // Create a new entity manager that contains entities generated from the root.
        $driver = new \Concrete\Core\Express\DoctrineMappingDriver($this->application, $this->getEntityManager());
        $driver->setNamespace($this->getNamespace());
        $factory = new BackendEntityManagerFactory($this->application, $this->getEntityManager(), $driver);
        $connection = $this->application['database']->connection();
        $em = $factory->create($connection);

        $metadata = $em->getClassMetadata($factory->getClassName($entity));
        $generator->generate(array($metadata), $this->outputPath);
    }

}
