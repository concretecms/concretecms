<?php
namespace Concrete\Core\Health\Report\Finding\Formatter;

use Concrete\Core\Health\Report\Finding\Controls\ControlsInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getIcon(): Element;

    public function getFindingEntryTextClass(): string;

    public function showControls(ControlsInterface $controls): bool;

    public function getType(): string;
}
