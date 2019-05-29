<?php

namespace Concrete\Core\Page\Sitemap\Event;

use SimpleXMLElement;
use Symfony\Component\EventDispatcher\GenericEvent;

class XmlReadyEvent extends GenericEvent
{
    /**
     * The sitemap XML document.
     *
     * @var \SimpleXMLElement
     */
    protected $document;

    /**
     * Initialize the instance.
     *
     * @param \SimpleXMLElement $document the sitemap XML document
     */
    public function __construct(SimpleXMLElement $document)
    {
        $this->document = $document;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\EventDispatcher\GenericEvent::getSubject()
     */
    public function getSubject()
    {
        return ['xmlDoc' => $this->document];
    }

    /**
     * Get the sitemap XML document.
     *
     * @return \SimpleXMLElement
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Replace the sitemap XML document.
     *
     * @param \SimpleXMLElement $newDocument
     *
     * @return $this
     */
    public function setDocument(SimpleXMLElement $newDocument)
    {
        $this->document = $newDocument;

        return $this;
    }
}
