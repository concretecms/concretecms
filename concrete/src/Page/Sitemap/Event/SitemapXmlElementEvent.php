<?php

namespace Concrete\Core\Page\Sitemap\Event;

use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @since 8.5.0
 */
class SitemapXmlElementEvent extends GenericEvent
{
    /**
     * For backward compatibility on < 8.5.0.
     *
     * @deprecated Use getElement instead.
     *
     * @return array
     */
    public function getSubject()
    {
        if (isset($this->subject['sitemapPage'])) {
            return $this->subject;
        }

        return ['sitemapPage' => $this->getElement()];
    }

    /**
     * Get the current XML element.
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapPage
     *
     * @return \Concrete\Core\Page\Sitemap\Element\SitemapElement
     */
    public function getElement()
    {
        return $this->getArgument('element');
    }

    /**
     * @param \Concrete\Core\Page\Sitemap\Element\SitemapElement $element
     */
    public function setElement($element)
    {
        $this->setArgument('element', $element);
    }
}
