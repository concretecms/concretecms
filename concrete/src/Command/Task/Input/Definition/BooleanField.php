<?php
namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Command\Task\Input\Field as LoadedField;
use Concrete\Core\Command\Task\Input\FieldInterface as LoadedFieldInterface;
use Concrete\Core\Console\Command\TaskCommand;
use Symfony\Component\Console\Input\InputOption;

defined('C5_EXECUTE') or die("Access Denied.");

class BooleanField extends Field
{

    public function __construct(string $key, string $label, string $description)
    {
        parent::__construct($key, $label, $description, false);
    }

    public function addToCommand(TaskCommand $command)
    {
        $command->addOption($this->getKey(), null, InputOption::VALUE_NONE, $this->getDescription());
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['type'] = FieldInterface::FIELD_TYPE_BOOLEAN;
        return $data;
    }

    public function loadFieldFromRequest(array $data): ?LoadedFieldInterface
    {
        if ($data[$this->getKey()] === '1') {
            return new LoadedField($this->getKey(), $data[$this->getKey()]);
        }
        return null;
    }




}
