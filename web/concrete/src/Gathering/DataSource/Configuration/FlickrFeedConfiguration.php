<?php
namespace Concrete\Core\Gathering\DataSource\Configuration;
use Loader;
class FlickrFeedConfiguration extends Configuration {

	public function setFlickrFeedTags($tags) {
		$this->tags = $tags;
	}

	public function getFlickrFeedTags() {
		return $this->tags;
	}

}
