<?php
namespace Concrete\Core\Entity\Page;

use Concrete\Core\Entity\PackageTrait;
use Doctrine\ORM\Mapping as ORM;
use HtmlObject\Image;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PageContainers"
 * )
 */
class Container
{
    
    use PackageTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $containerID;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $containerHandle = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $containerIcon = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $containerName = '';
    
    /**
     * @return mixed
     */
    public function getContainerID()
    {
        return $this->containerID;
    }

    /**
     * @return mixed
     */
    public function getContainerHandle()
    {
        return $this->containerHandle;
    }

    /**
     * @param mixed $containerHandle
     */
    public function setContainerHandle($containerHandle): void
    {
        $this->containerHandle = $containerHandle;
    }
    
    /**
     * @return mixed
     */
    public function getContainerName()
    {
        return $this->containerName;
    }

    /**
     * @param mixed $containerName
     */
    public function setContainerName($containerName): void
    {
        $this->containerName = $containerName;
    }

    /**
     * @return mixed
     */
    public function getContainerIcon()
    {
        return $this->containerIcon;
    }

    /**
     * @param mixed $containerIcon
     */
    public function setContainerIcon($containerIcon): void
    {
        $this->containerIcon = $containerIcon;
    }
   
    public function getContainerIconImage($asTag = true)
    {
        if ($this->getContainerIcon()) {
            $image = ASSETS_URL_IMAGES . '/icons/containers/' . $this->getContainerIcon();
            if ($asTag) {
                $image = new Image($image);
            }
            return $image;
        }
    }
    
   
    public function export(\SimpleXMLElement $node)
    {
        $container = $node->addChild('container');
        $container->addAttribute('handle', $this->getContainerHandle());
        $container->addAttribute('name', h($this->getContainerName()));
        $container->addAttribute('icon', $this->getContainerIcon());
        $container->addAttribute('package', $this->getPackageHandle());
    }

    
}
