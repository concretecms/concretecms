<?php
namespace Concrete\Core\Entity\Calendar\Summary;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Template\RenderableTemplateInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Entity
 * @ORM\Table(
 *     name="CalendarEventSummaryTemplates"
 * )
 */
class CalendarEventTemplate implements RenderableTemplateInterface
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Calendar\CalendarEvent", inversedBy="summary_templates")
     * @ORM\JoinColumn(name="eventID", referencedColumnName="eventID")
     */
    protected $event;
    
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
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event): void
    {
        $this->event = $event;
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

    /**
     * @return mixed
     */
    public function getData()
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

    
    
}
