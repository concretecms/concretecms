<?php
namespace Concrete\Core\Entity\Page;

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
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $containerID;

    /**
     * @ORM\Column(type="integer")
     */
    protected $containerThemeID = 0;

    /**
     * @ORM\Column(type="string")
     */
    protected $containerTemplateFile = '';

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
    public function getContainerThemeID()
    {
        return $this->containerThemeID;
    }

    /**
     * @param mixed $containerThemeID
     */
    public function setContainerThemeID($containerThemeID): void
    {
        $this->containerThemeID = $containerThemeID;
    }

    /**
     * @return mixed
     */
    public function getContainerTemplateFile()
    {
        return $this->containerTemplateFile;
    }

    /**
     * @param mixed $containerTemplateFile
     */
    public function setContainerTemplateFile($containerTemplateFile): void
    {
        $this->containerTemplateFile = $containerTemplateFile;
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

    
}
