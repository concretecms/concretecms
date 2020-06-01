<?php
namespace Concrete\Core\Application\UserInterface\Dashboard\Navigation;

use Concrete\Core\Navigation\Item\PageItem;
use Concrete\Core\Navigation\Navigation as BaseNavigation;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Navigation extends BaseNavigation implements DenormalizableInterface
{

    public function has(PageItem $pageItem)
    {
        foreach($this->getItems() as $item) {
            /**
             * @var $item PageItem
             */
            if ($pageItem->getPageID() == $item->getPageID()) {
                return true;
            }
        }
        return false;
    }

    public function remove(PageItem $pageItem) {
        $items = [];
        foreach($this->getItems() as $item) {
            /**
             * @var $item PageItem
             */
            if ($pageItem->getPageID() != $item->getPageID()) {
                $items[] = $item;
            }
        }
        $this->setItems($items);
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        foreach($data as $page) {
            $item = new PageItem();
            $item->setPageID($page['pageID']);
            $item->setUrl($page['url']);
            $item->setName($page['name']);
            $item->setIsActive($page['isActive']);
            $this->add($item);
        }
    }

}
