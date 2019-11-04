<?php
namespace Concrete\Core\Entity\Page\Container;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PageContainerInstanceAreas"
 * )
 */
class InstanceArea
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $containerInstanceAreaID;
    
    /**
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="instanceAreas")
     * @ORM\JoinColumn(name="containerInstanceID", referencedColumnName="containerInstanceID", onDelete="CASCADE")
     **/
    protected $instance;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $arID = 0;

    /**
     * @ORM\Column(type="string")
     */
    protected $containerAreaName = '';

    /**
     * @return mixed
     */
    public function getContainerInstanceAreaID()
    {
        return $this->containerInstanceAreaID;
    }
    
    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param mixed $instance
     */
    public function setInstance($instance): void
    {
        $this->instance = $instance;
    }

    /**
     * @return mixed
     */
    public function getAreaID()
    {
        return $this->arID;
    }

    /**
     * @param mixed $arID
     */
    public function setAreaID($arID): void
    {
        $this->arID = $arID;
    }

    /**
     * @return mixed
     */
    public function getContainerAreaName()
    {
        return $this->containerAreaName;
    }

    /**
     * @param mixed $containerAreaName
     */
    public function setContainerAreaName($containerAreaName): void
    {
        $this->containerAreaName = $containerAreaName;
    }


    


}
