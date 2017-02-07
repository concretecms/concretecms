<?php
namespace Concrete\Core\Form\Group;

use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Attribute\View as AttributeView;
use Concrete\Core\Form\Control\ControlInterface;
use Concrete\Core\Form\Control\ValueInterface;

abstract class View implements ViewInterface
{

    /**
     * @var $currentControl ControlInterface
     */
    protected $currentControl;

    /**
     * @var $currentValue ValueInterface
     */
    protected $currentValue;

    protected $context;
    protected $label;
    protected $supportsLabel = true;
    protected $isRequired = false;
    protected $templateLocator;
    protected $fieldWrapperTemplate;

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * @param boolean $isRequired
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;
    }

    /**
     * @return mixed
     */
    public function getFieldWrapperTemplate()
    {
        return $this->fieldWrapperTemplate;
    }

    /**
     * @param mixed $fieldWrapperTemplate
     */
    public function setFieldWrapperTemplate($fieldWrapperTemplate, $pkgHandle = null)
    {
        $this->fieldWrapperTemplate = [$fieldWrapperTemplate, $pkgHandle];
    }

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
        $this->templateLocator = new TemplateLocator();
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setSupportsLabel($supportsLabel)
    {
        $this->supportsLabel = $supportsLabel;
    }

    public function supportsLabel()
    {
        return $this->supportsLabel;
    }

    public function render(ControlInterface $control, ValueInterface $value = null)
    {
        $this->currentControl = $control;
        $this->currentValue = $value;

        $template = $this->getFieldWrapperTemplate();
        $pkgHandle = $template[1] ? $template[1] : null;
        $this->templateLocator->addLocation(
            DIRNAME_ELEMENTS .
            DIRECTORY_SEPARATOR .
            DIRNAME_FORM_CONTROL_WRAPPER_TEMPLATES .
            DIRECTORY_SEPARATOR .
            $template[0] . '.php',
        $pkgHandle);

        $view = $this;

        include($this->templateLocator->getFile());

        unset($this->currentControl);
        unset($this->currentValue);
    }

    public function renderControl()
    {
        if (!isset($this->currentControl)) {
            throw new \Exception(t('You may not call this method prior to calling render.'));
        }

        if (is_object($this->currentValue)) {
            $innerView = $this->currentValue->getControlView();
        } else {
            $innerView = $this->currentControl->getControlView();
        }

        $innerView->render($this->context);
    }

}
