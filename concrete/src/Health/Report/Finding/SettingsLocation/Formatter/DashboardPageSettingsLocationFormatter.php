<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation\Formatter;


use Concrete\Core\Health\Report\Finding\SettingsLocation\DashboardPageSettingsLocation;
use Concrete\Core\Health\Report\Finding\SettingsLocation\SettingsLocationInterface;
use HtmlObject\Element;

class DashboardPageSettingsLocationFormatter implements FormatterInterface
{

    /**
     * @param DashboardPageSettingsLocation $location
     * @return Element
     */
    public function getFindingsListElement(SettingsLocationInterface $location): Element
    {
        return new Element('a', $location->getPageName(), ['href' => $location->getPagePath(), 'class' => 'btn-sm btn btn-light']);
    }

}
