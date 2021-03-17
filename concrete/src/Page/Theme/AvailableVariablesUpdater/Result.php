<?php

namespace Concrete\Core\Page\Theme\AvailableVariablesUpdater;

use Concrete\Core\StyleCustomizer\Style\Style;
use Concrete\Core\StyleCustomizer\Style\Value\Value;

class Result
{
    /**
     * List of added values (array keys are strings representing a value, array values are the number of times the value has been added).
     *
     * @var int[]
     */
    protected $addedValues = [];

    /**
     * List of updated values (array keys are strings representing a value, array values are the number of times the value has been updated).
     *
     * @var int[]
     */
    protected $updatedValues = [];

    /**
     * List of invalid values (array keys are strings describing the warning, array values are the number of times the warning occurred).
     *
     * @var int[]
     */
    protected $removedInvalidValues = [];

    /**
     * List of values removed because duplicated (array keys are strings representing a value, array values are the number of times the value has been removed).
     *
     * @var int[]
     */
    protected $removedDuplicatedValues = [];

    /**
     * List of values removed because unused (array keys are strings representing a value, array values are the number of times the value has been removed).
     *
     * @var int[]
     */
    protected $removedUnusedValues = [];

    /**
     * List of generic warnings.
     *
     * @var string[]
     */
    protected $warnings = [];

    /**
     * List of variables defined in the theme XML files that don't have a value in any theme preset.
     *
     * @var \Concrete\Core\StyleCustomizer\Style\Style[]
     */
    protected $variablesWithoutValueInPresets = [];

    /**
     * Get a plain text-string representation of this instance.
     *
     * @return string
     */
    public function __toString()
    {
        $lines = array_merge(
            $this->describeValueList(t('added values'), $this->getAddedValues()),
            $this->describeValueList(t('updated values'), $this->getUpdatedValues()),
            $this->describeValueList(t('removed unused values'), $this->getRemovedUnusedValues()),
            $this->describeValueList(t('removed duplicated values'), $this->getRemovedDuplicatedValues()),
            $this->describeCountedStringList(t('removed invalid values'), $this->getRemovedInvalidValues()),
            $this->describeStringList(t('warnings'), $this->getWarnings())
        );

        return implode("\n", $lines);
    }

    /**
     * Add a value that has been added to an existing style list.
     *
     * @return $this
     */
    public function addAddedValue(Value $value)
    {
        $key = $this->getValueKey($value);
        if (isset($this->addedValues[$key])) {
            $this->addedValues[$key]++;
        } else {
            $this->addedValues[$key] = 1;
        }

        return $this;
    }

    /**
     * Get the list of the added values.
     *
     * @return int[] array keys are strings representing a value, array values are the number of times the value has been added
     */
    public function getAddedValues()
    {
        return $this->addedValues;
    }

    /**
     * Add a value that has been udated in an existing style list.
     *
     * @return $this
     */
    public function addUpdatedValue(Value $value)
    {
        $key = $this->getValueKey($value);
        if (isset($this->updatedValues[$key])) {
            $this->updatedValues[$key]++;
        } else {
            $this->updatedValues[$key] = 1;
        }

        return $this;
    }

    /**
     * Get the list of updated values.
     *
     * @return int[] array keys are strings representing a value, array values are the number of times the value has been updated
     */
    public function getUpdatedValues()
    {
        return $this->updatedValues;
    }

    /**
     * Add a value that has been removed from an existing style list because not valid.
     *
     * @param string $reason The reason why a value is invalid
     *
     * @return $this
     */
    public function addRemovedInvalidValue($reason)
    {
        if (isset($this->removedInvalidValues[$reason])) {
            $this->removedInvalidValues[$reason]++;
        } else {
            $this->removedInvalidValues[$reason] = 1;
        }

        return $this;
    }

    /**
     * Get the list of invalid values.
     *
     * @return int[] array keys are strings describing the warning, array values are the number of times the warning occurred
     */
    public function getRemovedInvalidValues()
    {
        return $this->removedInvalidValues;
    }

    /**
     * Add a value that has been removed from an existing style list because duplicated.
     *
     * @return $this
     */
    public function addRemovedDuplicatedValue(Value $value)
    {
        $key = $this->getValueKey($value);
        if (isset($this->removedDuplicatedValues[$key])) {
            $this->removedDuplicatedValues[$key]++;
        } else {
            $this->removedDuplicatedValues[$key] = 1;
        }

        return $this;
    }

    /**
     * Get the list of values removed because duplicated.
     *
     * @return int[] array keys are strings representing a value, array values are the number of times the value has been removed
     */
    public function getRemovedDuplicatedValues()
    {
        return $this->removedDuplicatedValues;
    }

    /**
     * Add a value that has been removed from an existing style list because unused.
     *
     * @return $this
     */
    public function addRemovedUnusedValue(Value $value)
    {
        $key = $this->getValueKey($value);
        if (isset($this->removedUnusedValues[$key])) {
            $this->removedUnusedValues[$key]++;
        } else {
            $this->removedUnusedValues[$key] = 1;
        }

        return $this;
    }

    /**
     * Get the list of values removed because unused.
     *
     * @return int[] array keys are strings representing a value, array values are the number of times the value has been removed
     */
    public function getRemovedUnusedValues()
    {
        return $this->removedUnusedValues;
    }

    /**
     * Add a generic warning.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function addWarning($value)
    {
        $this->warnings[] = $value;

        return $this;
    }

    /**
     * Get the list of generic warnings.
     *
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Add a variable defined in the theme XML file that doesn't have a value in any theme preset.
     *
     * @param bool $addToWarnings add an entry to the warnings too?
     *
     * @return $this
     */
    public function addVariableWithoutValueInPresets(Style $variable, $addToWarnings = true)
    {
        if (!in_array($variable, $this->variablesWithoutValueInPresets, true)) {
            $this->variablesWithoutValueInPresets[] = $variable;
            if ($addToWarnings) {
                $this->addWarning(t('No value found for the variable %1$s in any theme preset', $variable->getVariable()));
            }
        }

        return $this;
    }

    /**
     * Get the list of variables defined in the theme XML files that don't have a value in any theme preset.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Style[]
     */
    public function getVariablesWithoutValueInPresets()
    {
        return $this->variablesWithoutValueInPresets;
    }

    /**
     * Build a string that can be used to identify the type and name of a style value.
     *
     * @return string
     */
    protected function getValueKey(Value $value)
    {
        return get_class($value) . '@' . $value->getVariable();
    }

    /**
     * @param string $name
     * @param int[] $valueList
     *
     * @return string[]
     */
    protected function describeValueList($name, array $valueList)
    {
        if ($valueList === []) {
            return ["- {$name}: <" . t('none') . '>'];
        }
        $result = [];
        foreach ($valueList as $key => $count) {
            $result[] = '  - ' . $this->describeValueKey($key) . ': ' . t2(/*%s is a number*/ '%s time', '%s times', $count);
        }
        sort($result);

        return array_merge(["- {$name}:"], $result);
    }

    /**
     * @param string $name
     * @param int[] $valueList
     *
     * @return string[]
     */
    protected function describeCountedStringList($name, array $strings)
    {
        if ($strings === []) {
            return ["- {$name}: <" . t('none') . '>'];
        }
        $result = [];
        foreach ($strings as $string => $count) {
            $result[] = '  - ' . $string . ': ' . t2(/*%s is a number*/ '%s time', '%s times', $count);
        }
        sort($result);

        return array_merge(["- {$name}:"], $result);
    }

    /**
     * @param string $name
     * @param int[] $valueList
     *
     * @return string[]
     */
    protected function describeStringList($name, array $strings)
    {
        if ($strings === []) {
            return ["- {$name}: <" . t('none') . '>'];
        }
        $result = [];
        foreach ($strings as $string) {
            $result[] = '  - ' . $string;
        }
        sort($result);

        return array_merge(["- {$name}:"], $result);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function describeValueKey($key)
    {
        $prefix = 'Concrete\\Core\\StyleCustomizer\\Style\\Value\\';
        list($className, $variable) = explode('@', $key, 2);
        if (strpos($className, $prefix) === 0) {
            $className = substr($className, strlen($prefix));
        }

        return "{$variable} ({$className})";
    }
}
