<?php
namespace Concrete\Core\Command\Task\Input;

defined('C5_EXECUTE') or die("Access Denied.");

interface InputInterface extends \JsonSerializable
{

    public function hasField(string $key): bool;

    public function getField(string $key): ?FieldInterface;


}
