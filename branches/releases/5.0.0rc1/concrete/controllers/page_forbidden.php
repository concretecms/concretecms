<?php 

class PageForbiddenController extends Controller {
	
	public $helpers = array('form');
	
	public function view() {
		$this->set('intro_msg', 'You must sign in order to access this page!');
		$this->render('/login');
	}
	
}