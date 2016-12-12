<?php 
namespace Concrete\Core\Page\Type\PublishTarget\Type;
use Loader;
use Concrete\Core\Page\Type\Type as PageType;
use Page;

use \Concrete\Core\Page\Type\PublishTarget\Configuration\ParentPageConfiguration;
use Symfony\Component\HttpFoundation\Request;

class ParentPageType extends Type {

	public function configurePageTypePublishTarget(PageType $pt, $post) {
		$configuration = new ParentPageConfiguration($this);
		$configuration->setParentPageID($post['cParentID']);
		return $configuration;
	}

	public function configurePageTypePublishTargetFromImport($txml) {
		$configuration = new ParentPageConfiguration($this);
		$path = (string) $txml['path'];
		if (!$path) {
			$c = Page::getByID(HOME_CID);
		} else {
			$c = Page::getByPath($path);
		}
		$configuration->setParentPageID($c->getCollectionID());
		return $configuration;
	}

	public function validatePageTypeRequest(Request $request)
	{
		$e = parent::validatePageTypeRequest($request);
		$page = Page::getByID($request->request->get('cParentID'));
		if (!is_object($page) || $page->isError()) {
			$e->add(t('You must choose a valid parent page for pages of this type.'));
		}
		return $e;
	}
}