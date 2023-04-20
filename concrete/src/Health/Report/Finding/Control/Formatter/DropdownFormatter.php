<?php

namespace Concrete\Core\Health\Report\Finding\Control\Formatter;

use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use Concrete\Core\Health\Report\Finding\Control\DropdownControl;
use HtmlObject\Element;

class DropdownFormatter implements FormatterInterface
{

    /**
     * @param DropdownControl $control
     * @return Element
     */
    public function getFindingsListElement(ControlInterface $control, Finding $finding): Element
    {
        $menu = new Element('div', '', ['class' => 'dropdown-menu']);
        foreach ($control->getControls() as $innerControl) {
            $menu->appendChild($innerControl->getFormatter()->getFindingsListElement($innerControl, $finding));
        }

        $toggler = new Element(
            'button',
            '<i class="fa fa-cog"></i> ' . t('Actions'),
            ['class' => 'btn btn-sm btn-secondary dropdown-toggle', 'data-bs-toggle' => 'dropdown']
        );
        $container = new Element('div');
        $container->addClass('dropdown');
        $container->appendChild($toggler);
        $container->appendChild($menu);

        return $container;
    }

}
