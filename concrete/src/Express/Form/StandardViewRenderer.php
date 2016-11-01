<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Form\Context\ViewContext;

class StandardViewRenderer extends AbstractRenderer
{

    protected function getContext()
    {
        return new ViewContext($this->application, $this);
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

    public function render(Entry $entry = null)
    {
        $html = $this->getFormOpenTag();
        foreach ($this->getForm()->getFieldSets() as $fieldSet) {
            $html .= $this->renderFieldSet($fieldSet, $entry);
        }

        $html .= $this->getFormCloseTag();

        return $html;
    }

}
