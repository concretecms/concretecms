<?php
namespace Concrete\Core\StyleCustomizer\Style;
use Less_Parser;
class ValueList {

    protected $rules = array();

    public static function loadFromLessFile($file, $urlroot = false) {
        $l = new Less_Parser();
        $parser = $l->parseFile($file, $urlroot, true);
        $vl = new static();
        $vl->rules = $parser->rules;
        return $vl;
    }

    public function getRules() {
        return $this->rules;
    }

}