<?php
namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Console\Command\TaskCommand;
use Symfony\Component\Console\Input\InputOption;

defined('C5_EXECUTE') or die("Access Denied.");

class Definition implements \JsonSerializable
{

    /**
     * @var FieldInterface[]
     */
    protected $fields = [];

    public function addField(FieldInterface $field)
    {
        $this->fields[] = $field;
    }

    public function getFields()
    {
        return $this->fields;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'fields' => $this->fields,
        ];
    }

    public function addToCommand(TaskCommand $command)
    {
        foreach($this->getFields() as $field) {
            $field->addToCommand($command);
        }
    }
}
