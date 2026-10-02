<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Controller\Element\Dashboard\Express\Control\TextOptions;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Control\View\AuthorView;
use Concrete\Core\Express\Form\Control\View\PublicIdentifierView;
use Concrete\Core\Express\Form\Control\View\TextView;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Context\ContextInterface as ExpressContextInterface;
use Concrete\Core\Express\Form\Control\Renderer\TextEntityPropertyControlRenderer;
use Concrete\Core\Express\Form\Control\Template\Template;
use Concrete\Core\Express\Form\Control\Type\SaveHandler\TextControlSaveHandler;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressFormFieldSetPublicIdentifierControls")
 */
class PublicIdentifierControl extends Control
{

    public function getControlLabel()
    {
        return t('Public Identifier');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Control\ControlInterface::getControlView()
     *
     * @throws \InvalidArgumentException if $context is not a \Concrete\Core\Express\Form\Context\ContextInterface instance
     */
    public function getControlView(ContextInterface $context)
    {
        if (!$context instanceof ExpressContextInterface) {
            throw new \InvalidArgumentException(t('The form context must be an instance of %s.', ExpressContextInterface::class));
        }

        return new PublicIdentifierView($context, $this);
    }

    public function getType()
    {
        return 'entity_property';
    }

    public function getExporter()
    {
        return new \Concrete\Core\Export\Item\Express\Control\PublicIdentifierControl();
    }


}
