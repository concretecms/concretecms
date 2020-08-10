<?php
namespace Concrete\Core\Entity\Page\Container;

use Concrete\Core\Entity\Page\Container;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PageContainerInstances"
 * )
 */
class Instance
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $containerInstanceID;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Page\Container")
     * @ORM\JoinColumn(name="containerID", referencedColumnName="containerID", onDelete="CASCADE")
     **/
    protected $container;

    /**
     * @ORM\OneToMany(targetEntity="InstanceArea", mappedBy="instance", cascade={"remove"})
     */
    protected $instanceAreas;

    /**
     * @ORM\Column(type="boolean")
     **/
    protected $areasAreComputed = false;

    public function __construct()
    {
        $this->instanceAreas = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getContainerInstanceID()
    {
        return $this->containerInstanceID;
    }
    
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer($container): void
    {
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    public function areaAreasComputed()
    {
        return $this->areasAreComputed;
    }

    /**
     * @param mixed $areasAreComputed
     */
    public function setAreasAreComputed($areasAreComputed): void
    {
        $this->areasAreComputed = $areasAreComputed;
    }

    /**
     * @return InstanceArea[]
     */
    public function getInstanceAreas()
    {
        return $this->instanceAreas;
    }

    /**
     * @param mixed $instanceAreas
     */
    public function setInstanceAreas($instanceAreas): void
    {
        $this->instanceAreas = $instanceAreas;
    }
    
    
    
    

}
