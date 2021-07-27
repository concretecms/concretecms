<?php
namespace Concrete\Core\Command\Task\Input;

defined('C5_EXECUTE') or die("Access Denied.");

interface FieldInterface extends \JsonSerializable
{

    public function getKey(): string;

    public function getValue(): string;

}
