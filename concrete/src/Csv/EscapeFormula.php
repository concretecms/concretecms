<?php

namespace Concrete\Core\Csv;

/**
 * This file was ported from League CSV to be compatible with PHP 5.5.9 minimum
 *
 * League.Csv (https://csv.thephpleague.com).
 *
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @license https://github.com/thephpleague/csv/blob/master/LICENSE (MIT License)
 * @version 9.1.5
 * @link    https://github.com/thephpleague/csv
 */
use \InvalidArgumentException;

/**
 * A League CSV formatter to tackle CSV Formula Injection.
 *
 * @see http://georgemauer.net/2017/10/07/csv-injection.html
 *
 * @package League.csv
 * @since   9.1.0
 * @author  Ignace Nyamagana Butera <nyamsprod@gmail.com>
 */
class EscapeFormula
{
    /**
     * Spreadsheet formula starting character.
     */
    const FORMULA_STARTING_CHARS = '=-+@';

    /**
     * Effective Spreadsheet formula starting characters.
     *
     * @var array
     */
    protected $special_chars = [];

    /**
     * Escape character to escape each CSV formula field.
     *
     * @var string
     */
    protected $escape;

    /**
     * New instance.
     *
     * @param string   $escape        escape character to escape each CSV formula field
     * @param string[] $special_chars additional spreadsheet formula starting characters
     *
     */
    public function __construct($escape = "\t", array $special_chars = [])
    {
        $this->escape = $escape;
        if ([] !== $special_chars) {
            $special_chars = $this->filterSpecialCharacters($special_chars);
        }
        $chars = array_merge(str_split(self::FORMULA_STARTING_CHARS), $special_chars);
        $chars = array_unique($chars);
        $this->special_chars = array_fill_keys($chars, 1);
    }

    /**
     * Filter submitted special characters.
     *
     * @param string[] $characters
     *
     * @throws InvalidArgumentException if the string is not a single character
     *
     * @return string[]
     */
    protected function filterSpecialCharacters(array $characters)
    {
        foreach ($characters as $str) {
            if (1 != strlen($str)) {
                throw new InvalidArgumentException(sprintf('The submitted string %s must be a single character', $str));
            }
        }
        return $characters;
    }

    /**
     * Returns the list of character the instance will escape.
     *
     * @return string[]
     */
    public function getSpecialCharacters()
    {
        return array_keys($this->special_chars);
    }

    /**
     * Returns the escape character.
     *
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * League CSV formatter hook.
     *
     * @see escapeRecord
     */
    public function __invoke(array $record)
    {
        return $this->escapeRecord($record);
    }

    /**
     * Escape a CSV record.
     *
     * @return array
     */
    public function escapeRecord(array $record)
    {
        return array_map([$this, 'escapeField'], $record);
    }

    /**
     * Escape a CSV cell.
     *
     * @return string
     */
    protected function escapeField($cell)
    {
        if (!$this->isStringable($cell)) {
            return $cell;
        }
        $str_cell = (string) $cell;
        if (isset($str_cell[0], $this->special_chars[$str_cell[0]])) {
            return $this->escape.$str_cell;
        }
        return $cell;
    }

    /**
     * Tell whether the submitted value is stringable.
     *
     * @return bool
     */
    protected function isStringable($value)
    {
        return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
    }
}
