<?php
namespace Concrete\Core\Health\Report\Finding\Details\Formatter;


use Concrete\Core\Health\Report\Finding\Details\DetailsInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getFindingsListElement(DetailsInterface $details): Element;

    

}
