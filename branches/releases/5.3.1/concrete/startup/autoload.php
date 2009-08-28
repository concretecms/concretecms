<?php 

function __autoload($class) {
	$txt = Loader::helper('text');
	if (strpos($class, 'BlockController') > 0) {
		$class = substr($class, 0, strpos($class, 'BlockController'));
		$handle = $txt->uncamelcase($class);
		Loader::block($handle);
	} else if (strpos($class, 'Helper') > 0) {
		$class = substr($class, 0, strpos($class, 'Helper'));
		$handle = $txt->uncamelcase($class);
		Loader::helper($handle);
	}
}