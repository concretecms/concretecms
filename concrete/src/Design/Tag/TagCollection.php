<?php
namespace Concrete\Core\Design\Tag;

use Doctrine\Common\Collections\ArrayCollection;

class TagCollection
{

    /**
     * @var ArrayCollection
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function addTag(Tag $tag)
    {
        if (!$this->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function contains(Tag $tag): bool
    {
        foreach($this->tags as $existingTag) {
            if ($existingTag->getValue() == $tag->getValue()) {
                return true;
            }
        }
        return false;
    }

    public function getTags()
    {
        return $this->tags;
    }


}
