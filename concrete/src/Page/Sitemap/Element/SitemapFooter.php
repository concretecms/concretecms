<?php

namespace Concrete\Core\Page\Sitemap\Element;

use SimpleXMLElement;

/**
 * @since 8.4.1
 */
class SitemapFooter extends SitemapElement
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlLines()
     */
    public function toXmlLines($indenter = '  ')
    {
        return ['</urlset>'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlElement()
     */
    public function toXmlElement(SimpleXMLElement $parentElement = null)
    {
    }
}
