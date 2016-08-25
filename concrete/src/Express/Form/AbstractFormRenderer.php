<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Context\ViewContext;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractFormRenderer extends AbstractRenderer implements FormRendererInterface
{

    protected $requiredHtmlElement = '<span class="ccm-input-required">*</span>';

    /**
     * @return string
     */
    public function getRequiredHtmlElement()
    {
        return $this->requiredHtmlElement;
    }

    /**
     * @param string $requiredHtmlElement
     */
    public function setRequiredHtmlElement($requiredHtmlElement)
    {
        $this->requiredHtmlElement = $requiredHtmlElement;
    }

    protected function getCsrfTokenField()
    {
        return $this->application->make('token')->output('express_form', true);
    }

    protected function getFormIdentifierField(Form $form)
    {
        return '<input type="hidden" name="express_form_id" value="' . $form->getId() . '">';
    }

    public function render(Form $form, Entry $entry = null)
    {
        $html = $this->getFormOpenTag();
        $html .= $this->getFormIdentifierField($form);
        $html .= $this->getCsrfTokenField();
        foreach ($form->getFieldSets() as $fieldSet) {
            $html .= $this->renderFieldSet($fieldSet, $entry);
        }

        $html .= $this->getFormCloseTag();

        return $html;
    }



}
