<?php
namespace Concrete\Core\Health\Report\Finding\Details\Formatter;

use Concrete\Core\Filesystem\FileLocator\LocationInterface;
use Concrete\Core\Health\Report\Finding\Details\ButtonDetails;
use Concrete\Core\Health\Report\Finding\Details\DetailsInterface;
use HtmlObject\Element;

class ButtonFormatter implements FormatterInterface
{

    /**
     * @param ButtonDetails $details
     * @return Element
     */
    public function getFindingsListElement(DetailsInterface $details): Element
    {
        $location = $details->getLocation();
        return new Element('a', $location->getName(), ['href' => $location->getUrl(), 'class' => 'btn-sm btn btn-light']);
    }

}
