<?php
namespace Concrete\Core\Gathering\DataSource\Configuration;

class FlickrFeedConfiguration extends Configuration
{
    public function setFlickrFeedTags($tags)
    {
        $this->tags = $tags;
    }

    public function getFlickrFeedTags()
    {
        return $this->tags;
    }
}
