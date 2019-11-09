<?php
namespace Concrete\Core\Entity\Summary;

use Concrete\Core\Html\Image;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SummaryTemplates"
 * )
 */
class Template
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $category;

    /**
     * @ORM\Column(type="string")
     */
    protected $file = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $icon = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $name = '';

    /**
     * @ORM\Column(type="json")
     */
    protected $criteria;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon): void
    {
        $this->icon = $icon;
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

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param mixed $criteria
     */
    public function setCriteria($criteria): void
    {
        $this->criteria = $criteria;
    }
    
    public function getContainerIconImage($asTag = true)
    {
        if ($this->getIcon()) {
            $image = ASSETS_URL_IMAGES . '/icons/summary_templates/' . $this->getIcon();
            if ($asTag) {
                $image = new Image($image);
            }
            return $image;
        }
    }

    
}
