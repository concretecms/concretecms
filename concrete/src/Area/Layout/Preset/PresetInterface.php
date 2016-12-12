<?php
namespace Concrete\Core\Area\Layout\Preset;

interface PresetInterface
{

    public function getName();
    public function getColumns();
    public function getIdentifier();
    public function getFormatter();

}