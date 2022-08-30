<?php
namespace Concrete\Core\Health\Report\Finding\Formatter;

use Concrete\Core\Health\Report\Finding\Details\DetailsInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getIcon(): Element;

    public function getFindingEntryTextClass(): string;

    public function showDetails(DetailsInterface $details): bool;

    public function getType(): string;
}
