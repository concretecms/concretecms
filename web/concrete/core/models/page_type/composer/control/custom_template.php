<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTypeComposerControlCustomTemplate extends Object {

	protected $ptComposerControlCustomTemplateFilename;
	protected $ptComposerControlCustomTemplateName;

	public function __construct($ptComposerControlCustomTemplateFilename, $ptComposerControlCustomTemplateName) {
		$this->ptComposerControlCustomTemplateFilename = $ptComposerControlCustomTemplateFilename;
		$this->ptComposerControlCustomTemplateName = $ptComposerControlCustomTemplateName;
	}

	public function getPageTypeComposerControlCustomTemplateFilename() {return $this->ptComposerControlCustomTemplateFilename;}
	public function getPageTypeComposerControlCustomTemplateName() {return $this->ptComposerControlCustomTemplateName;}
	
	
}