<?php
namespace Concrete\Core\Page\Type\PublishTarget\Type;
use Loader;
use PageType;
use \Concrete\Core\Page\Type\PublishTarget\Configuration\PageTypeConfiguration;

class PageTypeType extends Type {


	public function configurePageTypePublishTarget(PageType $pt, $post) {
		$configuration = new PageTypeConfiguration($this);
		$configuration->setPageTypeID($post['ptID']);
		return $configuration;
	}

	public function configurePageTypePublishTargetFromImport($txml) {
		$configuration = new PageTypeConfiguration($this);
		$ct = PageType::getByHandle((string) $txml['pagetype']);
		$configuration->setPageTypeID($ct->getPageTypeID());
		return $configuration;
	}

	
}