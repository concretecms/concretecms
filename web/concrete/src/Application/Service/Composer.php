<?php
namespace Concrete\Core\Application\Service;
use Concrete\Core\Page\Type\Type;
use PageType;
use \Concrete\Core\Http\ResponseAssetGroup;
use \Concrete\Core\Page\Type\Composer\Control as PageTypeComposerControl;
use Loader;
class Composer {

	public function display(Type $pagetype, $page = false) {
        $pagetype->renderComposerOutputForm($page);
	}

	public function displayButtons(PageType $pagetype, $page = false) {
		Loader::element('page_types/composer/form/output/buttons', array(
			'pagetype' => $pagetype,
			'page' => $page
		));
	}

	public function addAssetsToRequest(PageType $pt, Controller $cnt) {
		$list = PageTypeComposerControl::getList($pt);
		foreach($list as $l) {
			$l->addAssetsToRequest($cnt);
		}
	}


}
