<?
namespace Concrete\Core\Application\Service;
use PageType;
use \Concrete\Core\Http\ResponseAssetGroup;
use \Concrete\Core\Page\Type\Composer\Control as PageTypeComposerControl;
use Loader;
class Composer {

	public function display(PageType $pagetype, $page = false) {
		Loader::element('page_types/composer/form/output/form', array(
			'pagetype' => $pagetype,
			'page' => $page
		));
	}

	public function displayButtons(PageType $pagetype, $page = false) {
		Loader::element('page_types/composer/form/output/buttons', array(
			'pagetype' => $pagetype,
			'page' => $page
		));
	}

	public function addAssetsToRequest(PageType $pt, Controller $cnt) {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/composer');
		$list = PageTypeComposerControl::getList($pt);
		foreach($list as $l) {
			$l->addAssetsToRequest($cnt);
		}
	}


}