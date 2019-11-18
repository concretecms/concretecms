<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Board\DataSource\Driver\DriverInterface;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Board\DataSource\Driver\Manager;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="BoardDataSources"
 * )
 */
class DataSource
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $dataSourceID;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $handle;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->dataSourceID;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle): void
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
    
    public function getDriver() : DriverInterface
    {
        $app = Facade::getFacadeApplication();
        $manager = $app->make(Manager::class);
        return $manager->driver($this->getHandle());
    }
    


}
