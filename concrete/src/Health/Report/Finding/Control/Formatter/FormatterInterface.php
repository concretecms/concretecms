<?php
namespace Concrete\Core\Health\Report\Finding\Control\Formatter;


use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getFindingsListElement(ControlInterface $control, Finding $finding): Element;

    

}
