<?
namespace Concrete\Core\Page\Theme\GridFramework\Type;
use Loader;
use Concrete\Core\Page\Theme\GridFramework;
class Bootstrap3 extends PageThemeGridFramework {

	public function getPageThemeGridFrameworkName() {
		return t('Twitter Bootstrap');
	}

	public function getPageThemeGridFrameworkRowStartHTML() {
		return '<div class="row">';
	}

	public function getPageThemeGridFrameworkRowEndHTML() {
		return '</div>';
	}

	public function getPageThemeGridFrameworkColumnClasses() {
		$columns = array(
			'col-md-1',
			'col-md-2',
			'col-md-3',
			'col-md-4',
			'col-md-5',
			'col-md-6',
			'col-md-7',
			'col-md-8',
			'col-md-9',
			'col-md-10',
			'col-md-11',
			'col-md-12'
		);
		return $columns;	
	}

	public function getPageThemeGridFrameworkColumnOffsetClasses() {
		$offsets = array(
			'col-md-offset-1',
			'col-md-offset-2',
			'col-md-offset-3',
			'col-md-offset-4',
			'col-md-offset-5',
			'col-md-offset-6',
			'col-md-offset-7',
			'col-md-offset-8',
			'col-md-offset-9',
			'col-md-offset-10',
			'col-md-offset-11',
			'col-md-offset-12'
		);
		return $offsets;	
	}


}
