<?
namespace Concrete\Core\Form\Widget;
class Rating {
	
	public function rating($prefix, $value = null, $includeJS = true) {
		$rt = Loader::helper('rating');
		return $rt->output($prefix, $value, true, $includeJS);
	}
	
	
}