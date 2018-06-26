<?php

namespace Concrete\Core\Page\Sitemap\Element;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\UrlInterface;
use SimpleXMLElement;

class SitemapPageAlternativeLanguage extends SitemapElement
{
    /**
     * The multilingual section associated to this alternative page.
     *
     * @var \Concrete\Core\Multilingual\Page\Section\Section
     */
    protected $section;

    /**
     * The alternative page in the alternative language.
     *
     * @var \Concrete\Core\Page\Page
     */
    protected $page;

    /**
     * The URL of the alternative page.
     *
     * @var \Concrete\Core\Url\UrlInterface
     */
    protected $url;

    /**
     * Should this alternative be skipped?
     *
     * @var bool
     */
    protected $skip = false;

    /**
     * The overridden hreflang value.
     *
     * @var string
     */
    protected $overriddenHrefLang = '';

    /**
     * @param \Concrete\Core\Multilingual\Page\Section\Section $section
     * @param \Concrete\Core\Page\Page $page
     * @param \Concrete\Core\Url\UrlInterface $url
     */
    public function __construct(Section $section, Page $page, UrlInterface $url)
    {
        $this->section = $section;
        $this->page = $page;
        $this->url = $url;
    }

    /**
     * Get the multilingual section associated to this alternative page.
     *
     * @return \Concrete\Core\Multilingual\Page\Section\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Get the alternative page in the alternative language.
     *
     * @return \Concrete\Core\Page\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the URL of the alternative page.
     *
     * @return \Concrete\Core\Url\UrlInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the URL of the alternative page.
     *
     * @param \Concrete\Core\Url\UrlInterface $url
     *
     * @return $this
     */
    public function setUrl(UrlInterface $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Should this alternative be skipped?
     *
     * @return bool
     */
    public function isSkip()
    {
        return $this->skip;
    }

    /**
     * Should this alternative be skipped?
     *
     * @param bool $skip
     *
     * @return $this
     */
    public function setSkip($skip)
    {
        $this->skip = (bool) $skip;

        return $this;
    }

    /**
     * Get the overridden hreflang value.
     *
     * @return string
     */
    public function getOverriddenHrefLang()
    {
        return $this->overriddenHrefLang;
    }

    /**
     * Set the overridden hreflang value.
     *
     * @param string $overriddenHrefLang
     *
     * @return $this

     */
    public function setOverriddenHrefLang($overriddenHrefLang)
    {
        $this->overriddenHrefLang = (string) $overriddenHrefLang;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlLines()
     */
    public function toXmlLines($indenter = '  ')
    {
        if ($this->isSkip()) {
            $result = null;
        } else {
            $nsn = static::MULTILINGUAL_NAMESPACE_NAME;
            $hreflang = h($this->getFinalHrefLang());
            $href = h((string) $this->getUrl());
            $result = [
                "{$indenter}{$indenter}<{$nsn}:link rel=\"alternate\" hreflang=\"{$hreflang}\" href=\"{$href}\" />",
            ];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlElement()
     */
    public function toXmlElement(SimpleXMLElement $parentElement = null)
    {
        if ($parentElement === null) {
            throw new UserMessageException(t('The sitemap XML link should not be the first element.'));
        }
        if ($this->isSkip()) {
            $result = null;
        } else {
            $result = $parentElement->addChild('link', null, static::MULTILINGUAL_NAMESPACE);
            $result->addAttribute('rel', 'alternate');
            $result->addAttribute('hreflang', $this->getFinalHrefLang());
            $result->addAttribute('href', (string) $this->getUrl());
        }

        return $result;
    }

    /**
     * Get the final value of the href lang.
     *
     * @return string
     */
    protected function getFinalHrefLang()
    {
        $result = $this->getOverriddenHrefLang();
        if ($result === '') {
            $result = strtolower(str_replace('_', '-', $this->getSection()->getLocale()));
        }

        return $result;
    }
}
