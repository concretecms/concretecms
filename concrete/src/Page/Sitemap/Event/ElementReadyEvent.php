<?php

namespace Concrete\Core\Page\Sitemap\Event;

use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use Concrete\Core\Page\Sitemap\Element\SitemapFooter;
use Concrete\Core\Page\Sitemap\Element\SitemapHeader;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\GenericEvent;

class ElementReadyEvent extends GenericEvent
{
    /**
     * The sitemap element (may be set to null by an event listener).
     *
     * @var \Concrete\Core\Page\Sitemap\Element\SitemapElement|null
     */
    protected $element;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Page\Sitemap\Element\SitemapElement $element
     */
    public function __construct(SitemapElement $element)
    {
        $this->element = $element;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\EventDispatcher\GenericEvent::getSubject()
     */
    public function getSubject()
    {
        return ['sitemapPage' => $this->element];
    }

    /**
     * Get the sitemap element (may be set to null by an event listener).
     *
     * @return \Concrete\Core\Page\Sitemap\Element\SitemapElement|null
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Replace the sitemap element (use NULL to skip a page).
     *
     * @param \Concrete\Core\Page\Sitemap\Element\SitemapElement|null $newElement
     *
     * @throws \InvalidArgumentException throws an InvalidArgumentException exception if you are setting to NULL the sitemap header or footer (just sitemap pages can be set to NULL)
     *
     * @return $this
     */
    public function setElement(SitemapElement $newElement = null)
    {
        if ($newElement === null) {
            if ($this->element instanceof SitemapHeader) {
                throw new InvalidArgumentException(t("Sitemap headers can't be omitted."));
            }
            if ($this->element instanceof SitemapFooter) {
                throw new InvalidArgumentException(t("Sitemap footers can't be omitted."));
            }
        }
        $this->element = $newElement;

        return $this;
    }
}
