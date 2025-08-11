<?php
namespace Concrete\Core\Health\Report\Finding\Formatter;

use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getIcon(): Element;

    public function getFindingEntryTextClass(): string;

    public function showControl(ControlInterface $control): bool;

    public function getType(): string;
}
