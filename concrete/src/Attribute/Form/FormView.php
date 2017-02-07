<?php
namespace Concrete\Core\Attribute\Form;

use Concrete\Core\Attribute\Context\ContextInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Filesystem\TemplateLocator;
use Concrete\Core\Attribute\View;

class FormView
{

    protected $currentKey;
    protected $context;
    protected $label;
    protected $supportsLabel = true;
    protected $isRequired = false;
    protected $templateLocator;
    protected $fieldWrapperTemplate;

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

    public function render(Key $key)
    {
        if (!isset($this->label)) {
            $this->setLabel($key->getAttributeKeyDisplayName());
        }

        $this->currentKey = $key;

        $template = $this->getFieldWrapperTemplate();
        $pkgHandle = $template[1] ? $template[1] : null;
        $this->templateLocator->addLocation(
            DIRNAME_ELEMENTS .
            DIRECTORY_SEPARATOR .
            DIRNAME_ATTRIBUTE .
            DIRECTORY_SEPARATOR .
            DIRNAME_ATTRIBUTE_CONTROL_WRAPPER_TEMPLATES .
            DIRECTORY_SEPARATOR .
            $template[0] . '.php',
        $pkgHandle);

        $view = $this;

        include($this->templateLocator->getFile());

        unset($this->currentKey);

    }

    public function renderControl()
    {
        if (!isset($this->currentKey)) {
            throw new \Exception(t('You may not call this method prior to calling render.'));
        }

        $innerView = new View($this->currentKey);
        $innerView->render($this->context);
    }

}
