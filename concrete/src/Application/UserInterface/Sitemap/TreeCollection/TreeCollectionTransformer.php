<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection;

use League\Fractal\TransformerAbstract;

class TreeCollectionTransformer extends TransformerAbstract
{

    /**
     * Convert an entry to an array
     * @param \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\EntryInterface $entry
     * @return array
     */
    private function transformEntry(Entry\EntryInterface $entry)
    {
        return [
            'id' => $entry->getID(),
            'name' => $entry->getLabel(),
            'icon' => (string) $entry->getIcon()
        ];
    }

    /**
     * Transform treecollections into lists of their entries
     *
     * @param \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollection $collection
     * @return array
     */
    public function transform(TreeCollection $collection)
    {
        return array_map([$this, 'transformEntry'], $collection->getEntries());
    }
}
