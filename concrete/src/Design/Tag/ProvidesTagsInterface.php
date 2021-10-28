<?php
namespace Concrete\Core\Design\Tag;

interface ProvidesTagsInterface
{

    /**
     * @return TagInterface[]
     */
    public function getDesignTags(): array;

}
