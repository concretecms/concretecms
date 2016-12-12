<?php
namespace Concrete\Core\Gathering\DataSource\Configuration;
use Loader;
class TwitterConfiguration extends Configuration {

	public function setTwitterUsername($username) {
		$this->username = $username;
	}

	public function getTwitterUsername() {
		return $this->username;
	}

}
