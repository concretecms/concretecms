<?php
namespace Concrete\Core\Health\Report\Finding\Controls\Formatter;

use Concrete\Core\Health\Report\Finding\Controls\ButtonControls;
use Concrete\Core\Health\Report\Finding\Controls\ControlsInterface;
use HtmlObject\Element;

class ButtonFormatter implements FormatterInterface
{

    /**
     * @param ButtonControls $controls
     * @return Element
     */
    public function getFindingsListElement(ControlsInterface $controls): Element
    {
        $location = $controls->getLocation();
        return new Element('a', $location->getName(), ['href' => $location->getUrl(), 'class' => 'btn-sm btn btn-light']);
    }

}
