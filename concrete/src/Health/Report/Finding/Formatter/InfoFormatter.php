<?php
namespace Concrete\Core\Health\Report\Finding\Formatter;

use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use HtmlObject\Element;

class InfoFormatter implements FormatterInterface
{

    public function getIcon(): Element
    {
        return new Element('i', '', ['class' => 'fa fa-info-circle']);
    }

    public function getFindingEntryTextClass(): string
    {
        return 'text-info';
    }

    public function showControl(ControlInterface $control): bool
    {
        return true;
    }

    public function getType(): string
    {
        return 'Notice';
    }




}
