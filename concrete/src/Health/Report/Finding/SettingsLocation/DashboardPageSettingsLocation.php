<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation;


use Concrete\Core\Health\Report\Finding\SettingsLocation\Formatter\DashboardPageSettingsLocationFormatter;
use Concrete\Core\Health\Report\Finding\SettingsLocation\Formatter\FormatterInterface;

abstract class DashboardPageSettingsLocation implements DashboardPageSettingsLocationInterface
{

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => static::class,
        ];
    }

    public function getFormatter(): FormatterInterface
    {
        return new DashboardPageSettingsLocationFormatter();
    }

}
