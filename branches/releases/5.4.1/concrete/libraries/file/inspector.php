<?php 

defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('file_attributes');

abstract class FileTypeInspector {

	abstract public function inspect($fv);

}
