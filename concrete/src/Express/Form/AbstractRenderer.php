<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Control\RendererInterface as ControlRendererInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRenderer implements RendererInterface
{
    protected $entityManager;
    protected $application;

    abstract protected function getFieldSetOpenTag(FieldSet $fieldSet);
    abstract protected function getFieldSetCloseTag(FieldSet $fieldSet);
    abstract protected function getContext();

    public function __construct(Application $application, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->application = $application;
    }

    protected function getFormOpenTag()
    {
        return '<div class="ccm-express-form">';
    }

    protected function getFormCloseTag()
    {
        return '</div>';
    }

    protected function renderFieldSet(FieldSet $fieldSet, Entry $entry = null)
    {
        $context = $this->getContext();
        $html = $this->getFieldSetOpenTag($fieldSet);
        /**
         * @var $control Control
         */
        foreach ($fieldSet->getControls() as $control) {
            /**
             * @var $renderer ControlRendererInterface
             */
            $renderer = $context->getControlRenderer($control);
            if (is_object($renderer)) {
                $html .= $renderer->render($this->getContext(), $control, $entry);
            }
        }
        $html .= $this->getFieldSetCloseTag($fieldSet);

        return $html;
    }

}
