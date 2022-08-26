<?php
namespace Concrete\Core\Health\Report\Finding\SettingsLocation;


use Concrete\Core\Health\Report\Finding\SettingsLocation\Formatter\FormatterInterface;

interface SettingsLocationInterface extends \JsonSerializable
{

    public function getFormatter(): FormatterInterface;

}
