<?php

namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Navigation\Navigation as BaseNavigation;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Navigation extends BaseNavigation implements DenormalizableInterface
{
    /**
     * Check if this instance contains a PageItem instance with the same page ID.
     *
     * @param \Concrete\Core\Navigation\Item\PageItem $pageItem
     *
     * @return bool
     */
    public function has(PageItem $pageItem): bool
    {
        foreach ($this->getItems() as $item) {
            if ($pageItem->getPageID() == $item->getPageID()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove (if present) the PageItems with the same page ID as the PageItem specified.
     *
     * @param \Concrete\Core\Navigation\Item\PageItem $pageItem
     *
     * @return self
     */
    public function remove(PageItem $pageItem): self
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if ($pageItem->getPageID() != $item->getPageID()) {
                $items[] = $item;
            }
        }
        $this->setItems($items);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Serializer\Normalizer\DenormalizableInterface::denormalize()
     */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        foreach ($data as $page) {
            $item = new PageItem();
            $item->setPageID($page['pageID']);
            $item->setUrl($page['url']);
            $item->setName($page['name']);
            $item->setIsActive($page['isActive']);
            $this->add($item);
        }
    }
}
