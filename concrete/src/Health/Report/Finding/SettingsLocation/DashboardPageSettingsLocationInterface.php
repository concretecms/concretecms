<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation;


use Concrete\Core\Health\Report\Finding\SettingsLocation\Formatter\FormatterInterface;

interface DashboardPageSettingsLocationInterface extends SettingsLocationInterface
{
    /**
     * @return string
     */
    public function getPageName(): string;

    /**
     * @return string
     */
    public function getPagePath(): string;


}
