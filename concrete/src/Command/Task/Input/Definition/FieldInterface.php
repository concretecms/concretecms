<?php
namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Command\Task\Input\FieldInterface as LoadedFieldInterface;
use Symfony\Component\Console\Input\InputInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface FieldInterface extends \JsonSerializable
{

    public function getKey() : string;

    public function getLabel() : string;

    public function getDescription() : string;

    public function loadFieldFromRequest(array $data): ?LoadedFieldInterface;

    public function loadFieldFromConsoleInput(InputInterface $consoleInput): ?LoadedFieldInterface;


}
