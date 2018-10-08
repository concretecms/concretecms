<?php

namespace Concrete\Core\Page\Sitemap\Event;

use Concrete\Core\Page\Page;
use SimpleXMLElement;
use Symfony\Component\EventDispatcher\GenericEvent;

class DeprecatedPageReadyEvent extends GenericEvent
{
    /**
     * The page for which we are building the sitemap node.
     *
     * @var \Concrete\Core\Page\Page
     */
    protected $page;

    /**
     * The sitemap XML node (may be set to null by an event listener).
     *
     * @var \SimpleXMLElement|null
     */
    protected $node;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Page\Page $page the page for which we are building the sitemap node
     * @param \SimpleXMLElement $node the sitemap XML node
     */
    public function __construct(Page $page, SimpleXMLElement $node)
    {
        $this->page = $page;
        $this->node = $node;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\EventDispatcher\GenericEvent::getSubject()
     */
    public function getSubject()
    {
        return ['page' => $this->getPage(), 'xmlNode' => $this->getNode()];
    }

    /**
     * Get the page for which we are building the sitemap node.
     *
     * @return \Concrete\Core\Page\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the sitemap XML node (may be set to null by an event listener).
     *
     * @return \SimpleXMLElement|null
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Replace the sitemap XML node (use NULL to skip this node).
     *
     * @param \SimpleXMLElement|null $newNode
     */
    public function setNode(SimpleXMLElement $newNode = null)
    {
        $this->node = $newNode;
    }
}
