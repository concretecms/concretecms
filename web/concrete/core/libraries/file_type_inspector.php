<?
defined('C5_EXECUTE') or die("Access Denied.");

if (!class_exists('Concrete5_Library_FileTypeInspector')) {

abstract class Concrete5_Library_FileTypeInspector {

	abstract public function inspect($fv);

}

}
