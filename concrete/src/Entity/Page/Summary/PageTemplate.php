<?php
namespace Concrete\Core\Entity\Page\Summary;

use Concrete\Core\Entity\Summary\GetTemplateDataTrait;
use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Template\RenderableTemplateInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Handles joining a page object to template objects. Every time a new page version is approved we 
 * rebuild this association against the page (much like the PagePath object). This doesn't join directly
 * to the HTML of template, but to the template object only.
 * 
 * A separate object or record on the page level will take care of what templates are selected for a page.
 * That way we don't have to rebuild those associations on every page version approval.
 * 
 * @ORM\Entity
 * @ORM\Table(
 *     name="PageSummaryTemplates"
 * )
 */
class PageTemplate implements RenderableTemplateInterface
{

    use GetTemplateDataTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $cID;
    
    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Summary\Template")
     */
    protected $template;

    /**
     * @ORM\Column(type="json")
     */
    protected $data;

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
    public function getTemplate() : Template
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate(Template $template): void
    {
        $this->template = $template;
    }
    
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'template' => $this->getTemplate(),
            'data' => $this->getData(),
            'id' => $this->getId(),
            'pageID' => $this->getPageID(),
        ];
        return $data;
    }


}
