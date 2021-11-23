<?php
namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Command\Task\Input\FieldInterface as LoadedFieldInterface;
use Concrete\Core\Console\Command\TaskCommand;
use Symfony\Component\Console\Input\InputInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface FieldInterface extends \JsonSerializable
{

    const FIELD_TYPE_INPUT = 'input';
    const FIELD_TYPE_SELECT = 'select';
    const FIELD_TYPE_BOOLEAN = 'boolean';

    public function getKey() : string;

    public function getLabel() : string;

    public function getDescription() : string;

    public function loadFieldFromRequest(array $data): ?LoadedFieldInterface;

    public function loadFieldFromConsoleInput(InputInterface $consoleInput): ?LoadedFieldInterface;

    public function isValid(LoadedFieldInterface $loadedField): bool;

    public function addToCommand(TaskCommand $command);

}
