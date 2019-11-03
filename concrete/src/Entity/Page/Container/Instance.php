<?php
namespace Concrete\Core\Entity\Page\Container;

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
     * @return mixed
     */
    public function getContainerInstanceID()
    {
        return $this->containerInstanceID;
    }
    
    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     */
    public function setContainer($container): void
    {
        $this->container = $container;
    }

}
