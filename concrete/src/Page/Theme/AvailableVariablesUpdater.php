<?php
namespace Concrete\Core\Page\Theme;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\StyleCustomizer\Style\Value\BasicValue;
use Concrete\Core\StyleCustomizer\Style\Value\TypeValue;
use Concrete\Core\StyleCustomizer\Style\Value\Value;
use Concrete\Core\StyleCustomizer\Style\ValueList;
use PDO;

class AvailableVariablesUpdater
{
    /**
     * Operation flag: no operations.
     *
     * @var int
     */
    const FLAG_NONE = 0b0000;

    /**
     * Operation flag: only simulate operations, don't persist anything.
     *
     * @var int
     */
    const FLAG_SIMULATE = 0b1;

    /**
     * Operation flag: delete invalid values.
     *
     * @var int
     */
    const FLAG_REMOVE_INVALID = 0b10;

    /**
     * Operation flag: delete duplicated values.
     *
     * @var int
     */
    const FLAG_REMOVE_DUPLICATED = 0b100;

    /**
     * Operation flag: delete values present in the database but not in any preset.
     *
     * @var int
     */
    const FLAG_REMOVE_UNUSED = 0b1000;

    /**
     * Operation flag: add values present in presets but not in the database.
     *
     * @var int
     */
    const FLAG_ADD = 0b10000;

    /**
     * Operation flag: update values present in presets and in the database, but with wrong definition.
     *
     * @var int
     */
    const FLAG_UPDATE = 0b100000;

    /**
     * The database connection.
     *
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $db;

    /**
     * Initialize the instance.
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Fix the values of every page theme.
     *
     * @param int $flags A combination of the values of the FLAG_... constants.
     *
     * @return array array keys are the theme handles, array values are the result of the fixTheme() method
     *
     * @see \Concrete\Core\Page\Theme\AvailableVariablesUpdater::fixTheme()
     */
    public function fixThemes($flags)
    {
        $stats = [];
        foreach (Theme::getList() as $theme) {
            $stats[$theme->getThemeHandle()] = $this->fixTheme($theme, $flags);
        }

        return $stats;
    }

    /**
     * Fix the values of a page theme.
     *
     * @param int $flags A combination of the values of the FLAG_... constants.
     *
     * @return array Array keys are:<ul>
     *               <li><code>added</code> the list of the names of the added variables</li>
     *               <li><code>updated</code> the list of the names of the updated variables</li>
     *               <li><code>removed_invalid</code> the list of errors explaining why some variables has been removed</li>
     *               <li><code>removed_duplicated</code> the list of the names of the variables removed because duplicated</li>
     *               <li><code>removed_unused</code> the list of the names of the variables removed because not used</li>
     *               <li><code>warnings</code> a list of warnings thrown while processing</li>
     *               </ul>
     */
    public function fixTheme(Theme $theme, $flags)
    {
        $stats = [
            'added' => [],
            'updated' => [],
            'removed_invalid' => [],
            'removed_duplicated' => [],
            'removed_unused' => [],
            'warnings' => [],
        ];
        if (!$theme->isThemeCustomizable()) {
            $stats['warnings'][] = t('The theme is not customizable');

            return $stats;
        }
        $presets = $theme->getThemeCustomizableStylePresets();
        if ($presets === []) {
            $stats['warnings'][] = t('The theme does not have presets');

            return $stats;
        }
        $flags = (int) $flags;
        $stats['_variablesWithoutValues'] = [];
        $simulate = (bool) ($flags & self::FLAG_SIMULATE);
        foreach ($this->listValueListIDs($theme) as $valueListID => $presetHandle) {
            $themeValueList = $this->buildThemeValueList($theme, $presets, $presetHandle, $stats);
            $currentValues = $this->listValues($valueListID);
            $currentValues = $this->processInvalid($currentValues, $stats, (bool) ($flags & self::FLAG_REMOVE_INVALID), $simulate);
            if ($flags && self::FLAG_REMOVE_DUPLICATED) {
                $currentValues = $this->deleteDuplicated($currentValues, $themeValueList, $stats, $simulate);
            }
            if ($flags && self::FLAG_REMOVE_UNUSED) {
                $currentValues = $this->deleteUnused($currentValues, $themeValueList, $stats, $simulate);
            }
            if ($flags && self::FLAG_UPDATE) {
                $currentValues = $this->updateCurrentValues($currentValues, $themeValueList, $stats, $simulate);
            }
            if ($flags & self::FLAG_ADD) {
                $currentValues = $this->addNewValues($currentValues, $themeValueList, $valueListID, $stats, $simulate);
            }
        }
        unset($stats['_variablesWithoutValues']);
        foreach (['added', 'updated', 'removed_invalid', 'removed_duplicated', 'removed_unused'] as $key) {
            $stats[$key] = array_values(array_unique($stats[$key]));
        }

        return $stats;
    }

    /**
     * Get the list of the IDs of the currently used variable lists and the associated preset.
     *
     * @return \Generator|string[] keys are the value list IDs, values are the preset name (empty string if none)
     */
    protected function listValueListIDs(Theme $theme)
    {
        $style = $theme->getThemeCustomStyleObject();
        if ($style) {
            $valueList = $style->getValueList();
            if ($valueList) {
                yield (int) $valueList->getValueListID() => (string) $style->getPresetHandle();
            }
        }
        $q = $this->db->createQueryBuilder();
        $x = $q->expr();
        $q
            ->from('CollectionVersionThemeCustomStyles', 't')
            ->select('distinct t.scvlID')
            ->where($x->eq('t.pThemeID', ':pThemeID'))->setParameter('pThemeID', $theme->getThemeID(), PDO::PARAM_INT)
            ->andWhere($x->isNotNull('t.scvlID'))
            ->andWhere($x->neq('t.scvlID', 0))
        ;
        $rs = $q->execute();
        while (($scvlID = $rs->fetchColumn()) !== false) {
            yield (int) $scvlID => '';
        }
    }

    /**
     * Build the list of all the style values defined by a theme.
     *
     * @param \Concrete\Core\StyleCustomizer\Preset[] $presets the presets provided by the theme (the default one should be the first one)
     * @param string $presetHandle The handle of the preferred preset (empty string if not available)
     *
     * @return \Concrete\Core\StyleCustomizer\Style\ValueList
     */
    protected function buildThemeValueList(Theme $theme, array $presets, $presetHandle, array &$stats)
    {
        if ((string) $presetHandle !== '') {
            // Prepend the preset with handle $presetHandle
            foreach (array_keys($presets) as $index) {
                $preset = $presets[$index];
                if ($preset->getPresetHandle() === $presetHandle) {
                    array_splice($presets, $index, 1);
                    array_unshift($presets, $preset);
                    break;
                }
            }
        }
        $themeValueList = new ValueList();
        foreach ($theme->getThemeCustomizableStyleList()->getSets() as $set) {
            foreach ($set->getStyles() as $style) {
                $styleValue = null;
                foreach ($presets as $preset) {
                    $styleValue = $style->getValueFromList($preset->getStyleValueList());
                    if ($styleValue !== null) {
                        break;
                    }
                }
                if ($styleValue !== null) {
                    $themeValueList->addValue($styleValue);
                } elseif (!in_array($style, $stats['_variablesWithoutValues'], true)) {
                    $stats['_variablesWithoutValues'][] = $style;
                    $stats['warnings'][] = t('No value found for the variable %1$s (set of styles: %2$s)', $style->getVariable(), $set->getDisplayName('text'));
                }
            }
        }

        return $themeValueList;
    }

    /**
     * List all the values of a list of a variables.
     *
     * @param int $valueListID
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value[]|string[] keys are the value IDs; in case of errors, values are strings
     */
    protected function listValues($valueListID)
    {
        $q = $this->db->createQueryBuilder();
        $x = $q->expr();
        $q
            ->from('StyleCustomizerValues', 't')
            ->select('t.scvID', 't.value')
            ->where($x->eq('t.scvlID', ':valueListID'))->setParameter('valueListID', $valueListID, PDO::PARAM_INT)
        ;
        $rs = $q->execute();
        $result = [];
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $result[(int) $row['scvID']] = $this->unserializeValue($row['value']);
        }

        return $result;
    }

    /**
     * Unserialize a serialized Style Customizer value.
     *
     * @param string $serializedValue
     *
     * @return \Concrete\Core\Attribute\Value\Value|string Returns a string in case of errors
     */
    protected function unserializeValue($serializedValue)
    {
        $error = '';
        set_error_handler(
            function ($errno, $errstr) use (&$error) {
                $error = (string) $errstr;
                if ($error === '') {
                    $error = t('Unknown error (code: %s)', (int) $errno);
                }
            },
            -1
        );
        $value = unserialize($serializedValue);
        restore_error_handler();
        if (!is_object($value)) {
            return $error === '' ? t('The function %s failed', 'unzerialize()') : $error;
        }
        if (!($value instanceof Value)) {
            return t('Wrong PHP class: expected %1$s but found %2$s', Value::class, get_class($value));
        }

        return $value;
    }

    /**
     * Save a new variable to the database.
     *
     * @param int $valueListID The associated list ID
     *
     * @return int the ID of the newly created record
     */
    protected function addValue($valueListID, Value $value)
    {
        $this->db->insert('StyleCustomizerValues', [
            'scvlID' => $valueListID,
            'value' => serialize($value),
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a value saved in the database.
     *
     * @param int $valueID the ID of the value
     */
    protected function updateValue($valueID, Value $value)
    {
        $this->db->update('StyleCustomizerValues', ['value' => serialize($value)], ['scvID' => $valueID]);
    }

    /**
     * Remove a value from the database.
     *
     * @param int $valueID the ID of the value to be removed
     */
    protected function deleteValue($valueID)
    {
        $this->db->delete('StyleCustomizerValues', ['scvID' => $valueID]);
    }

    /**
     * Process the list of values, filtering out the invalid ones.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value[]|string[] $currentValues keys are the value IDs; in case of errors, values are strings
     * @param bool $delete
     * @param bool $simulate
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value[] keys are the value IDs
     */
    protected function processInvalid(array $currentValues, array &$stats, $delete, $simulate)
    {
        $result = [];
        foreach ($currentValues as $currentValueID => $currentValue) {
            if (is_string($currentValue)) {
                if ($delete) {
                    if (!$simulate) {
                        $this->deleteValue($currentValueID);
                    }
                    $stats['removed_invalid'][] = $currentValue;
                } else {
                    $stats['warnings'][] = $currentValue;
                }
            } else {
                $result[$currentValueID] = $currentValue;
            }
        }

        return $result;
    }

    /**
     * Delete the duplicated values.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value[] $currentValues keys are the value IDs
     * @param bool $simulate
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value[] keys are the value IDs
     */
    protected function deleteDuplicated(array $currentValues, ValueList $themeValueList, array &$stats, $simulate)
    {
        $result = [];
        $dictionary = [];
        foreach ($currentValues as $currentValueID => $currentValue) {
            $dictionaryKey = get_class($currentValue) . '@' . $currentValue->getVariable();
            if (in_array($dictionaryKey, $dictionary, true)) {
                if (!$simulate) {
                    $this->deleteValue($currentValueID);
                }
                $stats['removed_duplicated'][] = $currentValue->getVariable();
            } else {
                $dictionary[] = $dictionaryKey;
                $result[$currentValueID] = $currentValue;
            }
        }

        return $result;
    }

    /**
     * Delete the unused values.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value[] $currentValues keys are the value IDs
     * @param bool $simulate
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value[] keys are the value IDs
     */
    protected function deleteUnused(array $currentValues, ValueList $themeValueList, array &$stats, $simulate)
    {
        $result = [];
        foreach ($currentValues as $currentValueID => $currentValue) {
            if ($this->isValueUnused($currentValue, $themeValueList, $stats)) {
                if (!$simulate) {
                    $this->deleteValue($currentValueID);
                }
                $stats['removed_unused'][] = $currentValue->getVariable();
            } else {
                $result[$currentValueID] = $currentValue;
            }
        }

        return $result;
    }

    /**
     * Check if a value is not used.
     *
     * @return bool
     */
    protected function isValueUnused(Value $value, ValueList $themeValueList, array $stats)
    {
        foreach ($themeValueList->getValues() as $presetValue) {
            if ($this->areValuesForTheSameVariable($value, $presetValue)) {
                return false;
            }
        }
        if ($value instanceof BasicValue && $value->getVariable() === 'preset-fonts-file') {
            return false;
        }
        foreach ($stats['_variablesWithoutValues'] as $style) {
            if ($style->getVariable() === $value->getVariable()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if some values needs to be updated.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value[] $currentValues keys are the value IDs
     * @param bool $simulate
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value[] keys are the value IDs
     */
    protected function updateCurrentValues(array $currentValues, ValueList $themeValueList, array &$stats, $simulate)
    {
        $result = [];
        foreach ($currentValues as $currentValueID => $currentValue) {
            $updatedCurrentValue = $this->buildUpdatedValue($currentValue, $themeValueList);
            if ($updatedCurrentValue !== null) {
                if (!$simulate) {
                    $this->updateValue($currentValueID, $updatedCurrentValue);
                }
                $result[$currentValueID] = $updatedCurrentValue;
                $stats['updated'][] = $updatedCurrentValue->getVariable();
            } else {
                $result[$currentValueID] = $currentValue;
            }
        }

        return $result;
    }

    /**
     * Create a new Value instance, if it needs to be fixed.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value|null NULL if the value doesn't need to be fixed
     */
    protected function buildUpdatedValue(Value $value, ValueList $themeValueList)
    {
        if ($value instanceof TypeValue) {
            return $this->buildUpdatedTypeValue($value, $themeValueList);
        }

        return null;
    }

    /**
     * Create a new TypeValue instance, if it needs to be fixed.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\TypeValue|null NULL if the value doesn't need to be fixed
     */
    protected function buildUpdatedTypeValue(TypeValue $value, ValueList $themeValueList)
    {
        $presetValue = null;
        foreach ($themeValueList->getValues() as $v) {
            if ($v instanceof TypeValue && $v->getVariable() === $value->getVariable()) {
                $presetValue = $v;
                break;
            }
        }
        if ($presetValue === null) {
            return null;
        }
        $result = clone $value;
        $fixed = false;
        $notSet = [-1, '-1'];
        if (in_array($result->getFontFamily(), $notSet, true) !== in_array($presetValue->getFontFamily(), $notSet, true)) {
            $result->setFontFamily($presetValue->getFontFamily());
            $fixed = true;
        }
        if (is_object($result->getFontSize()) !== is_object($presetValue->getFontSize())) {
            $result->setFontSize($presetValue->getFontSize());
            $fixed = true;
        }
        if (is_object($result->getColor()) !== is_object($presetValue->getColor())) {
            $result->setColor($presetValue->getColor());
            $fixed = true;
        }
        if (is_object($result->getLineHeight()) !== is_object($presetValue->getLineHeight())) {
            $result->setLineHeight($presetValue->getLineHeight());
            $fixed = true;
        }
        if (is_object($result->getLetterSpacing()) !== is_object($presetValue->getLetterSpacing())) {
            $result->setLetterSpacing($presetValue->getLetterSpacing());
            $fixed = true;
        }
        if (in_array($result->getFontStyle(), $notSet, true) !== in_array($presetValue->getFontStyle(), $notSet, true)) {
            $result->setFontStyle($presetValue->getFontStyle());
            $fixed = true;
        }
        if (in_array($result->getFontWeight(), $notSet, true) !== in_array($presetValue->getFontWeight(), $notSet, true)) {
            $result->setFontWeight($presetValue->getFontWeight());
            $fixed = true;
        }
        if (in_array($result->getTextDecoration(), $notSet, true) !== in_array($presetValue->getTextDecoration(), $notSet, true)) {
            $result->setTextDecoration($presetValue->getTextDecoration());
            $fixed = true;
        }
        if (in_array($result->getTextTransform(), $notSet, true) !== in_array($presetValue->getTextTransform(), $notSet, true)) {
            $result->setTextTransform($presetValue->getTextTransform());
            $fixed = true;
        }

        return $fixed ? $result : null;
    }

    /**
     * Check if some values needs to be added.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value[] $currentValues keys are the value IDs
     * @param int $valueListID
     * @param bool $simulate
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value[] $currentValues keys are the value IDs
     */
    protected function addNewValues(array $currentValues, ValueList $themeValueList, $valueListID, array &$stats, $simulate)
    {
        $result = $currentValues;
        foreach ($themeValueList->getValues() as $presetValue) {
            if ($this->shouldAddValue($presetValue, $currentValues)) {
                if (!$simulate) {
                    $currentValue = clone $presetValue;
                    $currentValueID = $this->addValue($valueListID, $currentValue);
                    $result[$currentValueID] = $result;
                }
                $stats['added'][] = $presetValue->getVariable();
            }
        }

        return $result;
    }

    /**
     * Check if a value found in a preset should be added to the currently used values.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value $presetValue
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value[] $currentValues
     *
     * @return bool
     */
    protected function shouldAddValue($presetValue, array $currentValues)
    {
        if (!($presetValue instanceof Value)) {
            return false;
        }
        foreach ($currentValues as $currentValue) {
            if ($this->areValuesForTheSameVariable($presetValue, $currentValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if two variables are the values of the same variable.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value|mixed $value1
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value|mixed $value2
     *
     * @return bool
     */
    protected function areValuesForTheSameVariable($value1, $value2)
    {
        if (!($value1 instanceof Value)) {
            return false;
        }
        if (!($value2 instanceof Value)) {
            return false;
        }
        if ((string) $value1->getVariable() !== (string) $value2->getVariable()) {
            return false;
        }
        if (get_class($value1) !== get_class($value2)) {
            return false;
        }

        return true;
    }
}
