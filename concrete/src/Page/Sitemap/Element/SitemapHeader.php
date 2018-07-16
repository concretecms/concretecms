<?php

namespace Concrete\Core\Page\Sitemap\Element;

use Concrete\Core\Error\UserMessageException;
use SimpleXMLElement;

class SitemapHeader extends SitemapElement
{
    /**
     * Is multilingual supported?
     *
     * @var bool
     */
    protected $isMultilingual;

    /**
     * Initialize the instance.
     *
     * @param bool $isMultilingual Is multilingual supported?
     */
    public function __construct($isMultilingual)
    {
        $this->setIsMultilingual($isMultilingual);
    }

    /**
     * Is multilingual supported?
     *
     * @return bool
     */
    public function isIsMultilingual()
    {
        return $this->isMultilingual;
    }

    /**
     * Is multilingual supported?
     *
     * @param bool $isMultilingual
     *
     * @return $this
     */
    public function setIsMultilingual($isMultilingual)
    {
        $this->isMultilingual = (bool) $isMultilingual;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlLines()
     */
    public function toXmlLines($indenter = '  ')
    {
        return [
            $this->getXmlMetaHeader(),
            $this->getUrlset(false),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlElement()
     */
    public function toXmlElement(SimpleXMLElement $parentElement = null)
    {
        if ($parentElement !== null) {
            throw new UserMessageException(t('The sitemap XML header should be the first element.'));
        }

        return new SimpleXMLElement($this->getXmlMetaHeader() . $this->getUrlset(true));
    }

    /**
     * @param bool $selfClosing
     *
     * @return string[]
     */
    protected function getUrlset($selfClosing)
    {
        $result = '<urlset xmlns="' . static::DEFAULT_NAMESPACE . '"';
        if ($this->isIsMultilingual()) {
            $result .= ' xmlns:' . static::MULTILINGUAL_NAMESPACE_NAME . '="' . static::MULTILINGUAL_NAMESPACE . '"';
        }
        $result .= $selfClosing ? '/>' : '>';

        return $result;
    }
}
