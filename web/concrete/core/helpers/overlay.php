<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Helper_Overlay {
	
	public function init($selector = '.ccm-overlay') {
		$view = View::getInstance();
		$view->addHeaderItem(Loader::helper('html')->css('overlay/jquery.magnific-popup.css'));
		$view->addFooterItem(Loader::helper('html')->javascript('overlay/jquery.magnific-popup.js'));
		if($selector && strlen($selector)) {
			$js = "<script type=\"text/javascript\">$(function() {
				$('".$selector."-image').magnificPopup({type:'image'});
				$('".$selector."-iframe').magnificPopup({type:'image'});
				$('".$selector."-inline').magnificPopup({type:'image'});
				$('".$selector."-ajax').magnificPopup({type:'image'});
			});</script>";
			$view->addFooterItem($js);
		}
	}
}