<?php 

defined('C5_EXECUTE') or die("Access Denied.");

class PhpFileTypeInspector extends FileTypeInspector {
	
	public function inspect($fv) {
		
		$path = $fv->getPath();
		$ft = FileTypeList::getInstance();
		$ft->defineImporterAttribute('lines', t('Lines of Code'), 'NUMBER', false);
		$at1 = FileAttributeKey::getByHandle('lines');
		$fv->setAttribute($at1, trim(exec('/bin/cat \'' . $path . '\' | wc -l')));
		
	}
	

}