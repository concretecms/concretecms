<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Controller\Element\Dashboard\Express\Control\TextOptions;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Control\Form\TextEntityPropertyControlFormRenderer;
use Concrete\Core\Express\Form\Control\Type\SaveHandler\TextControlSaveHandler;
use Concrete\Core\Express\Form\Control\View\TextEntityPropertyControlViewRenderer;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressFormFieldSetTextControls")
 */
class TextControl extends Control
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $text;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    public function getControlSaveHandler()
    {
        return new TextControlSaveHandler();
    }

    public function getFormControlRenderer(Entity $entity = null)
    {
        return new TextEntityPropertyControlFormRenderer($entity);
    }

    public function getViewControlRenderer(Entry $entry)
    {
        return new TextEntityPropertyControlViewRenderer($entry);
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
}
