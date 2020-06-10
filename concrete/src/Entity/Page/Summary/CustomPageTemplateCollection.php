<?php
namespace Concrete\Core\Entity\Page\Summary;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="PageSummaryTemplateCustomCollection"
 * )
 */
class CustomPageTemplateCollection
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $cID;
    
    /**
     * @ORM\ManyToMany(targetEntity="Concrete\Core\Entity\Summary\Template")
     * @ORM\JoinTable(name="PageSummaryTemplateCustomCollectionTemplates",
     *      joinColumns={@ORM\JoinColumn(name="cID", referencedColumnName="cID")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="template_id", referencedColumnName="id")}
     *      )
     */
    protected $templates;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getPageID()
    {
        return $this->cID;
    }

    /**
     * @param mixed $cID
     */
    public function setPageID($cID): void
    {
        $this->cID = $cID;
    }

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     */
    public function setTemplates($templates): void
    {
        $this->templates = $templates;
    }


    

    
    
}
