<?php
namespace Concrete\Core\Health\Grade\Formatter;

use HtmlObject\Element as HtmlElement;
use Concrete\Core\Filesystem\Element;

interface FormatterInterface
{

    /**
     * @return HtmlElement
     */
    public function getResultsListIcon(): HtmlElement;

    /**
     * The main score/notice element shown at the top of the report
     * @return Element
     */
    public function getBannerElement(): Element;


}
