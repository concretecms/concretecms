<?php

declare(strict_types=1);

namespace Concrete\Core\Command\Task\Input\Definition;

use Concrete\Core\Command\Task\Input\Field as LoadedField;
use Concrete\Core\Command\Task\Input\FieldInterface as LoadedFieldInterface;
use Concrete\Core\Console\Command\TaskCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

defined('C5_EXECUTE') or die('Access Denied.');

class IntegerField extends Field
{
    /**
     * The minimum allowed value (NULL if no limit).
     *
     * @var int|null
     */
    protected $min;

    /**
     * The maximum allowed value (NULL if no limit).
     *
     * @var int|null
     */
    protected $max;

    /**
     * @param int|null $min the minimum allowed value (NULL if no limit)
     * @param int|null $max the maximum allowed value (NULL if no limit)
     */
    public function __construct(string $key, string $label, string $description, ?int $min = null, ?int $max = null, bool $isRequired = false, ?string $shortcut = null)
    {
        parent::__construct($key, $label, $description, $isRequired, $shortcut);
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Get the minimum allowed value (NULL if no limit).
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * Get the maximum allowed value (NULL if no limit).
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Command\Task\Input\Definition\Field::isValid()
     */
    public function isValid(LoadedFieldInterface $loadedField): bool
    {
        $value = trim($loadedField->getValue());
        if ($value === '') {
            if ($this->isRequired()) {
                throw new \Exception(t('Field "%s" is required.', $loadedField->getKey()));
            }

            return true;
        }
        $int = (int) $value;
        if ((string) $int !== $value) {
            throw new \Exception(t('The value of the field "%s" must be an integer number.', $loadedField->getKey()));
        }
        if ($this->min !== null && $int < $this->min) {
            throw new \Exception(t('The value of the field "%1$s" must be greater than or equal to %2$s.', $loadedField->getKey(), $this->min));
        }
        if ($this->max !== null && $int > $this->max) {
            throw new \Exception(t('The value of the field "%1$s" must be less than or equal to %2$s.', $loadedField->getKey(), $this->max));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Command\Task\Input\Definition\Field::loadFieldFromRequest()
     */
    public function loadFieldFromRequest(array $data): ?LoadedFieldInterface
    {
        $value = $data[$this->getKey()] ?? '';
        $value = is_scalar($value) ? trim((string) $value) : '';
        if ($value === '' && !$this->isRequired()) {
            return null;
        }

        return new LoadedField($this->getKey(), $value);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Command\Task\Input\Definition\Field::loadFieldFromConsoleInput()
     */
    public function loadFieldFromConsoleInput(InputInterface $consoleInput): ?LoadedFieldInterface
    {
        $loadedField = parent::loadFieldFromConsoleInput($consoleInput);
        if ($loadedField === null) {
            return null;
        }

        return new LoadedField($this->getKey(), trim($loadedField->getValue()));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Command\Task\Input\Definition\Field::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['type'] = FieldInterface::FIELD_TYPE_INTEGER;
        $data['min'] = $this->getMin();
        $data['max'] = $this->getMax();

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Command\Task\Input\Definition\Field::addToCommand()
     */
    public function addToCommand(TaskCommand $command)
    {
        if ($this->isRequired()) {
            $command->addArgument($this->getKey(), InputArgument::REQUIRED, $this->getConsoleDescription());
        } else {
            $command->addOption($this->getKey(), $this->getShortcut(), InputOption::VALUE_REQUIRED, $this->getConsoleDescription());
        }
    }

    protected function getConsoleDescription(): string
    {
        $description = $this->getDescription();
        if ($this->min !== null && $this->max !== null) {
            $description .= ' ' . t('Valid values are integers between %1$s and %2$s.', $this->min, $this->max);
        } elseif ($this->min !== null) {
            $description .= ' ' . t('Valid values are integers greater than or equal to %s.', $this->min);
        } elseif ($this->max !== null) {
            $description .= ' ' . t('Valid values are integers less than or equal to %s.', $this->max);
        }

        return $description;
    }
}
