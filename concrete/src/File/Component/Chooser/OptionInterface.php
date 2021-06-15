<?php
namespace Concrete\Core\File\Component\Chooser;

interface OptionInterface extends \JsonSerializable
{

    public function getComponentKey(): string;

    public function getTitle(): string;

}