<?php

namespace Concrete\Core\Page\Sitemap\Element;

use SimpleXMLElement;

abstract class SitemapElement
{
    /**
     * The default sitemap namespace.
     *
     * @var string
     */
    const DEFAULT_NAMESPACE = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    /**
     * The namespace required for the multilingual alternatives.
     *
     * @var string
     */
    const MULTILINGUAL_NAMESPACE = 'http://www.w3.org/1999/xhtml';

    /**
     * The alias of the namespace required for the multilingual alternatives.
     *
     * @var string
     */
    const MULTILINGUAL_NAMESPACE_NAME = 'x';

    /**
     * Returns the XML representation of this element.
     *
     * @param string $indenter The string used to indent the XML
     *
     * @return string[]|null Returns NULL in case no data should be generated, the XML lines otherwise
     */
    abstract public function toXmlLines($indenter = '  ');

    /**
     * Returns a SimpleXMLElement instance representing this element.
     *
     * @param null|SimpleXMLElement $parentElement
     *
     * @return \SimpleXMLElement|null
     */
    abstract public function toXmlElement(SimpleXMLElement $parentElement = null);

    /**
     * Get the XML meta-header.
     *
     * @return string
     */
    protected function getXmlMetaHeader()
    {
        return '<?xml version="1.0" encoding="' . APP_CHARSET . '"?>';
    }
}
