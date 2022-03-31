<?php
namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Command\Task\Input\FieldInterface as LoadedFieldInterface;
use Concrete\Core\Console\Command\TaskCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

defined('C5_EXECUTE') or die("Access Denied.");

class SelectField extends Field
{

    /**
     * @var array
     */
    protected $options;

    public function __construct(string $key, string $label, string $description, array $options, bool $isRequired = false)
    {
        parent::__construct($key, $label, $description, $isRequired);
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function isValid(LoadedFieldInterface $loadedField): bool
    {
        $options = $this->getOptions();
        if ($loadedField->getValue()) {
            if (!array_key_exists($loadedField->getValue(), $options)) {
                throw new \Exception(
                    t(
                        'Option value "%s" is not a valid option for "%s"',
                        $loadedField->getValue(),
                        $loadedField->getKey()
                    )
                );
            }
            return true;
        } else {
            return parent::isValid($loadedField);
        }
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['options'] = $this->getOptions();
        $data['type'] = FieldInterface::FIELD_TYPE_SELECT;
        return $data;
    }

    protected function getConsoleDescription()
    {
        $description = $this->getDescription();
        $options = implode(', ', array_keys($this->getOptions()));
        $description .= t(' Valid options include %s', $options);
        return $description;
    }


    public function addToCommand(TaskCommand $command)
    {
        if ($this->isRequired()) {
            $command->addArgument($this->getKey(), InputArgument::REQUIRED, $this->getConsoleDescription());
        } else {
            $command->addOption($this->getKey(), null, InputOption::VALUE_REQUIRED, $this->getConsoleDescription());
        }
    }


}
