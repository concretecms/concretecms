<?php
namespace \Concrete\Core\Gathering\DataSource\Configuration;
class RssFeed extends Configuration {

	public function setRssFeedURL($url) {
		$this->url = $url;
	}

	public function getRssFeedURL() {
		return $this->url;
	}

}
