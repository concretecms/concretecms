<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Express\Form\Context\FormContext;

class StandardFormRenderer extends AbstractFormRenderer
{

    protected function getContext()
    {
        return new FormContext($this->application, $this);
    }

    protected function getFieldSetOpenTag(FieldSet $set)
    {
        $html = '<fieldset class="ccm-express-form-field-set">';
        if ($set->getTitle()) {
            $html .= '<legend>' . $set->getTitle() . '</legend>';
        }

        return $html;
    }

    protected function getFieldSetCloseTag(FieldSet $set)
    {
        return '</fieldset>';
    }

}
