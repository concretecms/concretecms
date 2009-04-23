<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('file_attributes');

abstract class FileTypeInspector {

	abstract public function inspect($fv);

}
