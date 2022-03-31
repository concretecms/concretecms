<?php
namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Command\Task\Input\FieldInterface as LoadedFieldInterface;
use Concrete\Core\Command\Task\Input\Field as LoadedField;
use Concrete\Core\Console\Command\TaskCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

defined('C5_EXECUTE') or die("Access Denied.");

class Field implements FieldInterface
{

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $isRequired = false;

    public function __construct(string $key, string $label, string $description, bool $isRequired = false)
    {
        $this->key = $key;
        $this->label = $label;
        $this->description = $description;
        $this->isRequired = $isRequired;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function isValid(LoadedFieldInterface $loadedField): bool
    {
        if ($this->isRequired() && !$loadedField->getValue()) {
            throw new \Exception(t('Field "%s" is required.', $loadedField->getKey()));
        }
        return true;
    }

    public function loadFieldFromRequest(array $data): ?LoadedFieldInterface
    {
        return new LoadedField($this->getKey(), $data[$this->getKey()]);
    }

    public function loadFieldFromConsoleInput(InputInterface $consoleInput): ?LoadedFieldInterface
    {
        if ($this->isRequired()) {
            // this is an option.
            if ($consoleInput->hasArgument($this->getKey()) && $consoleInput->getArgument($this->getKey()) != '') {
                return new LoadedField($this->getKey(), $consoleInput->getArgument($this->getKey()));
            }
        } else {
            if ($consoleInput->hasOption($this->getKey()) && $consoleInput->getOption($this->getKey()) != '') {
                return new LoadedField($this->getKey(), $consoleInput->getOption($this->getKey()));
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'type' => FieldInterface::FIELD_TYPE_INPUT,
            'label' => $this->getLabel(),
            'key' => $this->getKey(),
            'description' => $this->getDescription(),
            'isRequired' => $this->isRequired(),
        ];
    }

    public function addToCommand(TaskCommand $command)
    {
        if ($this->isRequired()) {
            $command->addArgument($this->getKey(), InputArgument::REQUIRED, $this->getDescription());
        } else {
            $command->addOption($this->getKey(), null, InputOption::VALUE_REQUIRED, $this->getDescription());
        }
    }

}
