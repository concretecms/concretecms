<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation\Formatter;


use Concrete\Core\Health\Report\Finding\SettingsLocation\SettingsLocationInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getFindingsListElement(SettingsLocationInterface $location): Element;

    

}
