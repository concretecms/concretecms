<?php 
namespace Concrete\Core\Feed;
use \Concrete\Core\Foundation\Service\Group as ServiceGroup;

class FeedServiceGroup extends ServiceGroup {

	public function register() {
		$singletons = array(
			'feed' => '\Concrete\Core\Feed\FeedService'
		);

		foreach($singletons as $key => $value) {
			$this->locator->singleton($key, $value);
		}
	}


}