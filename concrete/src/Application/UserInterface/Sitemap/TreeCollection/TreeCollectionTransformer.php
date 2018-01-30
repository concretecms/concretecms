<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use League\Fractal\TransformerAbstract;

class TreeCollectionTransformer extends TransformerAbstract
{

    public function transform(TreeCollection $collection)
    {
        $entries = [];
        foreach($collection->getEntries() as $entry) {
            $o = new \stdClass();
            $o->id = $entry->getID();
            $o->name = $entry->getLabel();
            $o->icon = (string) $entry->getIcon();
            $entries[] = $o;
        }
        return $entries;

    }

}
