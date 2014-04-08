<?php 
namespace Concrete\Core\Page\Type\PublishTarget\Type;
use Loader;
use PageType;
use \Concrete\Core\Page\Type\PublishTarget\Configuration\AllConfiguration;

class AllType extends Type {

	public function configurePageTypePublishTarget(PageType $pt, $post) {
		$configuredTarget = new AllConfiguration($this);
		return $configuredTarget;
	}

	public function configurePageTypePublishTargetFromImport($txml) {
		return new AllConfiguration($this);
	}

}