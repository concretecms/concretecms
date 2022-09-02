<?php
namespace Concrete\Core\Health\Report\Finding\Controls\Formatter;


use Concrete\Core\Health\Report\Finding\Controls\ControlsInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getFindingsListElement(ControlsInterface $controls): Element;

    

}
