<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_ComposerControlCustomTemplate extends Object {

	protected $cmpControlCustomTemplateFilename;
	protected $cmpControlCustomTemplateName;

	public function __construct($cmpControlCustomTemplateFilename, $cmpControlCustomTemplateName) {
		$this->cmpControlCustomTemplateFilename = $cmpControlCustomTemplateFilename;
		$this->cmpControlCustomTemplateName = $cmpControlCustomTemplateName;
	}

	public function getComposerControlCustomTemplateFilename() {return $this->cmpControlCustomTemplateFilename;}
	public function getComposerControlCustomTemplateName() {return $this->cmpControlCustomTemplateName;}
	
	
}