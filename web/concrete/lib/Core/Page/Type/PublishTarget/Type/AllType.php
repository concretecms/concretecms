<?php 
namespace Concrete\Core\Page\Type\PublishTarget\Type;
use Loader;
class AllType extends Type {

	public function configurePageTypePublishTarget(PageType $pt, $post) {
		$configuredTarget = new AllPageTypePublishTargetConfiguration($this);
		return $configuredTarget;
	}

	public function configurePageTypePublishTargetFromImport($txml) {
		return new AllPageTypePublishTargetConfiguration($this);
	}

}