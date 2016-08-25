<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Controller\Element\Dashboard\Express\Control\TextOptions;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\Renderer\TextEntityPropertyControlRenderer;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\Control\Type\SaveHandler\TextControlSaveHandler;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressFormFieldSetTextControls")
 */
class TextControl extends Control
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $headline;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @return mixed
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param mixed $headline
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


    public function getControlSaveHandler()
    {
        return new TextControlSaveHandler();
    }

    public function getControlRenderer(ContextInterface $context)
    {
        return new TextEntityPropertyControlRenderer();
    }

    public function getControlLabel()
    {
        return t('Text');
    }

    public function getType()
    {
        return 'entity_property';
    }

    public function getControlOptionsController()
    {
        return new TextOptions($this);
    }

    public function getExporter()
    {
        return new \Concrete\Core\Export\Item\Express\Control\TextControl();
    }


}
