<?php
namespace Concrete\Core\StyleCustomizer;

class StyleList
{
    protected $sets = array();

    public static function loadFromXMLFile($file)
    {
        $sx = simplexml_load_file($file);

        return static::loadFromXMLElement($sx);
    }

    public static function loadFromXMLElement(\SimpleXMLElement $sx)
    {
        $parser = new StyleListParser($sx);

        return $parser->parse();
    }

    public function addSet($name)
    {
        $s = new Set();
        $s->setName($name);
        $this->sets[] = $s;

        return $s;
    }

    public function getSets()
    {
        return $this->sets;
    }
}
