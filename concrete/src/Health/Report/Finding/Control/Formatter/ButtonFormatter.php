<?php
namespace Concrete\Core\Health\Report\Finding\Control\Formatter;

use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Health\Report\Finding\Control\ButtonControl;
use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use HtmlObject\Element;

class ButtonFormatter implements FormatterInterface
{

    /**
     * @param ButtonControl $controls
     * @return Element
     */
    public function getFindingsListElement(ControlInterface $control, Finding $finding): Element
    {
        $location = $control->getLocation();
        return new Element('a', $location->getName(), ['href' => $location->getUrl(), 'class' => 'btn-sm btn btn-light']);
    }

}
