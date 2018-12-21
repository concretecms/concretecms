<?php

namespace Concrete\Core\StyleCustomizer;

class StyleList
{
    /**
     * The list of the style sets.
     *
     * @var \Concrete\Core\StyleCustomizer\Set[]
     */
    protected $sets = [];

    /**
     * Get the list of style sets defined in an XML file.
     *
     * @param string $file The full path of the XML file
     *
     * @return self
     */
    public static function loadFromXMLFile($file)
    {
        $sx = simplexml_load_file($file);

        return static::loadFromXMLElement($sx);
    }

    /**
     * Get the list of style sets defined in a SimpleXML element.
     *
     * @param \SimpleXMLElement $sx
     *
     * @return self
     */
    public static function loadFromXMLElement(\SimpleXMLElement $sx)
    {
        $parser = new StyleListParser($sx);

        return $parser->parse();
    }

    /**
     * Add a new empty style set.
     *
     * @param string $name the name of the style set
     *
     * @return \Concrete\Core\StyleCustomizer\Set
     */
    public function addSet($name)
    {
        $s = new Set();
        $s->setName($name);
        $this->sets[] = $s;

        return $s;
    }

    /**
     * Get the list of the style sets.
     *
     * @return \Concrete\Core\StyleCustomizer\Set[]
     */
    public function getSets()
    {
        return $this->sets;
    }
}
