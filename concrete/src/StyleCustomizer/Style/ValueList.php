<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\StyleCustomizer\Style\Value\BasicValue;
use Concrete\Core\StyleCustomizer\Style\Value\Value;
use Concrete\Core\StyleCustomizer\StyleList;
use Concrete\Core\Support\Facade\Application;
use Less_Parser;
use Symfony\Component\HttpFoundation\ParameterBag;

class ValueList
{
    /**
     * The identifier of this value list instance.
     *
     * @var int|null
     */
    protected $scvlID;

    /**
     * The list of values.
     *
     * @var \Concrete\Core\StyleCustomizer\Style\Value\Value[]
     */
    protected $values = [];

    /**
     * Get the identifier of this value list instance.
     *
     * @return int|null
     */
    public function getValueListID()
    {
        return $this->scvlID;
    }

    /**
     * Get the list of values.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Extract the value list from a received data.
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $request
     * @param \Concrete\Core\StyleCustomizer\StyleList $styles
     *
     * @return static
     */
    public static function loadFromRequest(ParameterBag $request, StyleList $styles)
    {
        $vl = new static();
        foreach ($styles->getSets() as $set) {
            foreach ($set->getStyles() as $style) {
                $value = $style->getValueFromRequest($request);
                if ($value) {
                    $vl->addValue($value);
                }
            }
        }

        if ($request->has('preset-fonts-file')) {
            $bv = new BasicValue('preset-fonts-file');
            $bv->setValue($request->get('preset-fonts-file'));
            $vl->addValue($bv);
        }

        return $vl;
    }

    /**
     * Extract the value list from a LESS file.
     *
     * @param string $file the full path of the LESS file
     * @param string $urlroot The url of the file
     *
     * @return static
     */
    public static function loadFromLessFile($file, $urlroot = '')
    {
        $l = new Less_Parser();
        $parser = $l->parseFile($file, $urlroot, true);
        $vl = new static();
        $rules = $parser->rules;

        // load required preset variables.
        foreach ($rules as $rule) {
            if (preg_match('/@preset-fonts-file/i', isset($rule->name) ? $rule->name : '', $matches)) {
                $value = $rule->value->value[0]->value[0]->value;
                $bv = new BasicValue('preset-fonts-file');
                $bv->setValue($value);
                $vl->addValue($bv);
            }
        }

        foreach ([
            ColorStyle::class,
            TypeStyle::class,
            ImageStyle::class,
            SizeStyle::class,
        ] as $type) {
            $values = call_user_func([$type, 'getValuesFromVariables'], $rules);
            $vl->addValues($values);
        }

        return $vl;
    }

    /**
     * Get a value list from the database.
     *
     * @param int $scvlID the identifier of the value list
     *
     * @return static|null
     */
    public static function getByID($scvlID)
    {
        $o = null;
        if ($scvlID) {
            $app = Application::getFacadeApplication();
            $db = $app->make(Connection::class);
            $scvlID = (int) $db->fetchColumn('select scvlID from StyleCustomizerValueLists where scvlID = ?', [$scvlID]);
            if ($scvlID !== 0) {
                $o = new static();
                $o->scvlID = $scvlID;
                $rows = $db->fetchAll('select * from StyleCustomizerValues where scvlID = ?', [$scvlID]);
                foreach ($rows as $row) {
                    $o->addValue(unserialize($row['value']));
                }
            }
        }

        return $o;
    }

    /**
     * Persist this list of values.
     */
    public function save()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        if (!isset($this->scvlID)) {
            $db->insert('StyleCustomizerValueLists', []);
            $this->scvlID = $db->LastInsertId();
        } else {
            $db->delete('StyleCustomizerValues', ['scvlID' => $this->scvlID]);
        }

        foreach ($this->values as $value) {
            $db->insert('StyleCustomizerValues', ['value' => serialize($value), 'scvlID' => $this->scvlID]);
        }
    }

    /**
     * Add a value.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value $value
     *
     * @return $this
     */
    public function addValue(Value $value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Add a list of values.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value[]|\Traversable $values
     *
     * @return $this
     */
    public function addValues($values)
    {
        foreach ($values as $value) {
            $this->addValue($value);
        }

        return $this;
    }
}
