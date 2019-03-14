<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Controller\Element\Dashboard\Express\Control\TextOptions;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Control\View\AuthorView;
use Concrete\Core\Express\Form\Control\View\TextView;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\Renderer\TextEntityPropertyControlRenderer;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\Control\Type\SaveHandler\TextControlSaveHandler;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressFormFieldSetAuthorControls")
 */
class AuthorControl extends Control
{

    public function getControlLabel()
    {
        return t('Author');
    }

    public function getControlView(ContextInterface $context)
    {
        return new AuthorView($context, $this);
    }

    public function getType()
    {
        return 'entity_property';
    }

    public function getExporter()
    {
        return new \Concrete\Core\Export\Item\Express\Control\AuthorControl();
    }


}
