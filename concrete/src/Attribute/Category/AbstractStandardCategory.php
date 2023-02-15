<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Attribute\Key\Key;

abstract class AbstractStandardCategory extends AbstractCategory implements StandardCategoryInterface
{
    use StandardCategoryTrait {
        delete as deleteCategory;
    }

    public function delete()
    {
        parent::delete();
        $this->deleteCategory();
    }

    public function add($type, $key, $settings = null, $pkg = null)
    {
        /**
         * @var Key $key
         */
        $key = parent::add($type, $key, $settings, $pkg);
        $key->setAttributeCategoryEntity($this->getCategoryEntity());
        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key;
    }

    protected function getAttributeValueEntity(string $cacheKey, array $parameters)
    {
        /** @var RequestCache $cache */
        $cache = $this->application->make('cache/request');
        $item = $cache->getItem($cacheKey);
        if ($item->isHit()) {
            return $item->get();
        }

        $r = $this->getAttributeValueRepository();
        $value = $r->findOneBy($parameters);

        if ($item->isMiss()) {
            $cache->save($item->set($value));
        }

        return $value;
    }
}
