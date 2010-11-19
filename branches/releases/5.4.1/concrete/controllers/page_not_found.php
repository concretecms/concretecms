<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class PageNotFoundController extends Controller {
	
	public $helpers = array('form');
	
	public function view() {
		header("HTTP/1.0 404 Not Found");
	}
	
}