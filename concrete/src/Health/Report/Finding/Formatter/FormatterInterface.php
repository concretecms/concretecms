<?php
namespace Concrete\Core\Health\Report\Finding\Formatter;

use Concrete\Core\Health\Report\Finding\SettingsLocation\SettingsLocationInterface;
use HtmlObject\Element;

interface FormatterInterface
{

    public function getIcon(): Element;

    public function getFindingEntryTextClass(): string;

    public function showSettingsLocation(SettingsLocationInterface $settingsLocation): bool;

    public function getType(): string;
}
