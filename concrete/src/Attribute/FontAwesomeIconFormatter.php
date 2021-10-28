<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Html\Service\FontAwesomeIcon;
use Concrete\Core\Support\Facade\Application;
use HtmlObject\Element;

class FontAwesomeIconFormatter implements IconFormatterInterface
{
    protected $icon;

    public function __construct($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return Element
     */
    public function getListIconElement()
    {
        $app = Application::getFacadeApplication();
        $icon = $app->make(FontAwesomeIcon::class, ['name' => $this->icon]);
        $icon->setFixedWidth(true);
        $element = $icon->getTag();
        $element->addClass('ccm-attribute-icon');

        return $element;
    }
}
