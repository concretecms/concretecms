<?php

namespace Concrete\Core\Design\Tag;

class TagInspector
{

    public function getTags(ProvidesTagsInterface $object): TagCollection
    {
        $collection = new TagCollection();
        $tags = $object->getDesignTags();
        foreach($tags as $tag) {
            $collection->addTag(new Tag($tag->getValue())); // normalize this into a simpler object.
        }
        return $collection;
    }

}