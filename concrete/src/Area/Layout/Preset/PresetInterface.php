<?php
namespace Concrete\Core\Area\Layout\Preset;

/**
 * @since 5.7.5
 */
interface PresetInterface
{
    public function getName();
    public function getColumns();
    public function getIdentifier();
    public function getFormatter();
}
