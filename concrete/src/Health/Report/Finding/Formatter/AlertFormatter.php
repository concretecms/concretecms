<?php
namespace Concrete\Core\Health\Report\Finding\Formatter;

use Concrete\Core\Health\Report\Finding\SettingsLocation\SettingsLocationInterface;
use HtmlObject\Element;

class AlertFormatter implements FormatterInterface
{

    public function getIcon(): Element
    {
        return new Element('i', '', ['class' => 'fa fa-exclamation-circle']);
    }

    public function getFindingEntryTextClass(): string
    {
        return 'text-danger';
    }

    public function showSettingsLocation(SettingsLocationInterface $settingsLocation): bool
    {
        return true;
    }

    public function getType(): string
    {
        return 'Alert';
    }

}
