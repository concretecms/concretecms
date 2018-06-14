<?php

namespace Concrete\Core\Page\Sitemap\Element;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Url\UrlInterface;
use DateTime;
use SimpleXMLElement;

class SitemapPage extends SitemapElement
{
    /**
     * The page.
     *
     * @var \Concrete\Core\Page\Page
     */
    protected $page;

    /**
     * The URL of the page.
     *
     * @var \Concrete\Core\Url\UrlInterface
     */
    protected $url;

    /**
     * The last modification date/time.
     *
     * @var \DateTime|null
     */
    protected $lastMod;

    /**
     * The page change frequency.
     *
     * @var string
     */
    protected $changeFrequency;

    /**
     * The page priority.
     *
     * @var string
     */
    protected $priority;

    /**
     * The pages in alternative languages mapped to this page.
     *
     * @var \Concrete\Core\Page\Sitemap\Element\SitemapPageAlternativeLanguage[]
     */
    protected $alternativeLanguages = [];

    /**
     * Should this page be skipped?
     *
     * @var bool
     */
    protected $skip = false;

    /**
     * @param Page $page the page
     * @param UrlInterface $url the URL of the page
     * @param \DateTime|null the last modification date/time
     * @param string $changeFrequency the page change frequency
     * @param string $priority the page priority
     * @param null|DateTime $lastMod
     */
    public function __construct(Page $page, UrlInterface $url, DateTime $lastMod = null, $changeFrequency = '', $priority = '')
    {
        $this->page = $page;
        $this->setUrl($url);
        $this->setLastMod($lastMod);
        $this->setChangeFrequency($changeFrequency);
        $this->setPriority($priority);
    }

    /**
     * Get the page.
     *
     * @return \Concrete\Core\Page\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the URL of the page.
     *
     * @return \Concrete\Core\Url\UrlInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the URL of the page.
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
     * Get the last modification date/time.
     *
     * @return \DateTime|null
     */
    public function getLastMod()
    {
        return $this->lastMod;
    }

    /**
     * Set the last modification date/time.
     *
     * @param \DateTime|null $lastMod
     *
     * @return $this
     */
    public function setLastMod(DateTime $lastMod = null)
    {
        $this->lastMod = $lastMod;

        return $this;
    }

    /**
     * Get the page change frequency.
     *
     * @return string
     */
    public function getChangeFrequency()
    {
        return $this->changeFrequency;
    }

    /**
     * Set the page change frequency.
     *
     * @param string $changeFrequency
     *
     * @return $this
     */
    public function setChangeFrequency($changeFrequency)
    {
        $this->changeFrequency = (string) $changeFrequency;

        return $this;
    }

    /**
     * Get the page priority.
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the page priority.
     *
     * @param string $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = (string) $priority;

        return $this;
    }

    /**
     * Get the pages in alternative languages mapped to this page.
     *
     * @return \Concrete\Core\Page\Sitemap\Element\SitemapPageAlternativeLanguage[]
     */
    public function getAlternativeLanguages()
    {
        return $this->alternativeLanguages;
    }

    /**
     * Add a page in an alternative language that's mapped to this page.
     *
     * @param \Concrete\Core\Page\Sitemap\Element\SitemapPageAlternativeLanguage $alternative
     *
     * @return $this
     */
    public function addAlternativeLanguage(SitemapPageAlternativeLanguage $alternative)
    {
        $this->alternativeLanguages[] = $alternative;

        return $this;
    }

    /**
     * Should this page be skipped?
     *
     * @return bool
     */
    public function isSkip()
    {
        return $this->skip;
    }

    /**
     * Should this page be skipped?
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
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlLines()
     */
    public function toXmlLines($indenter = '  ')
    {
        if ($this->isSkip()) {
            $result = null;
        } else {
            if ($indenter) {
                $prefix = $indenter;
                $prefix2 = $indenter . $indenter;
            } else {
                $prefix = '';
                $prefix = '';
            }
            $loc = h((string) $this->getUrl());
            $result = [
                "{$prefix}<url>",
                "{$prefix2}<loc>{$loc}</loc>",
            ];
            $lastMod = $this->getLastMod();
            if ($lastMod !== null) {
                $lastMod = $lastMod->format(DateTime::ATOM);
                $result[] = "{$prefix2}<lastmod>{$lastMod}</lastmod>";
            }
            $changeFrequency = $this->getChangeFrequency();
            if ($changeFrequency !== '') {
                $result[] = "{$prefix2}<changefreq>{$changeFrequency}</changefreq>";
            }
            $priority = $this->getPriority();
            if ($priority !== '') {
                $result[] = "{$prefix2}<priority>{$priority}</priority>";
            }
            foreach ($this->getAlternativeLanguages() as $alternativeLanguage) {
                $result = array_merge($result, $alternativeLanguage->toXmlLines($indenter));
            }
            $result[] = "{$prefix}</url>";
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
            throw new UserMessageException(t('The sitemap XML page should not be the first element.'));
        }
        $result = $parentElement->addChild('url');
        $result->addChild('loc', (string) $this->getUrl());
        $lastMod = $this->getLastMod();
        if ($lastMod !== null) {
            $result->addChild('lastmod', $lastMod->format(DateTime::ATOM));
        }
        $changeFrequency = $this->getChangeFrequency();
        if ($changeFrequency !== '') {
            $result->addChild('changefreq', $changeFrequency);
        }
        $priority = $this->getPriority();
        if ($priority !== '') {
            $result->addChild('priority', $priority);
        }
        foreach ($this->getAlternativeLanguages() as $alternativeLanguage) {
            $alternativeLanguage->toXmlElement($result);
        }

        return $result;
    }
}
