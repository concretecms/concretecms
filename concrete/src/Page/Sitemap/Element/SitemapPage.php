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
     * Change frequency: always (pagess change each time they are accessed).
     *
     * @var string
     */
    const CHANGEFREQUENCY_ALWAYS = 'always';

    /**
     * Change frequency: hourly.
     *
     * @var string
     */
    const CHANGEFREQUENCY_HOURLY = 'hourly';

    /**
     * Change frequency: daily.
     *
     * @var string
     */
    const CHANGEFREQUENCY_DAILY = 'daily';

    /**
     * Change frequency: weekly.
     *
     * @var string
     */
    const CHANGEFREQUENCY_WEEKLY = 'weekly';

    /**
     * Change frequency: monthly.
     *
     * @var string
     */
    const CHANGEFREQUENCY_MONTHLY = 'monthly';

    /**
     * Change frequency: yearly.
     *
     * @var string
     */
    const CHANGEFREQUENCY_YEARLY = 'yearly';

    /**
     * Change frequency: never (should be used to describe archived URLs).
     *
     * @var string
     */
    const CHANGEFREQUENCY_NEVER = 'never';

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
    protected $lastModifiedAt;

    /**
     * The page change frequency (one of the SitemapPage::CHANGEFREQUENCY_... constants, or an empty string in unavailable).
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
     * @param \DateTime|null $lastModifiedAt the last modification date/time
     * @param string $changeFrequency the page change frequency (one of the SitemapPage::CHANGEFREQUENCY_... constants, or an empty string in unavailable)
     * @param string $priority the page priority
     */
    public function __construct(Page $page, UrlInterface $url, DateTime $lastModifiedAt = null, $changeFrequency = '', $priority = '')
    {
        $this->page = $page;
        $this->setUrl($url);
        $this->setLastModifiedAt($lastModifiedAt);
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
    public function getLastModifiedAt()
    {
        return $this->lastModifiedAt;
    }

    /**
     * Set the last modification date/time.
     *
     * @param \DateTime|null $lastModifiedAt
     *
     * @return $this
     */
    public function setLastModifiedAt(DateTime $lastModifiedAt = null)
    {
        $this->lastModifiedAt = $lastModifiedAt;

        return $this;
    }

    /**
     * Get the page change frequency (one of the SitemapPage::CHANGEFREQUENCY_... constants, or an empty string in unavailable).
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
     * @param string $changeFrequency one of the SitemapPage::CHANGEFREQUENCY_... constants, or an empty string in unavailable
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
            $lastModifiedAt = $this->getLastModifiedAt();
            if ($lastModifiedAt !== null) {
                $lastModifiedAt = $lastModifiedAt->format(DateTime::ATOM);
                $result[] = "{$prefix2}<lastmod>{$lastModifiedAt}</lastmod>";
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
        $lastModifiedAt = $this->getLastModifiedAt();
        if ($lastModifiedAt !== null) {
            $result->addChild('lastmod', $lastModifiedAt->format(DateTime::ATOM));
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
