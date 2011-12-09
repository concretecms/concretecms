<?php

function CoreAutoload($class) {
	$txt = Loader::helper('text');
	if ($class == 'DashboardBaseController') { 
		Loader::controller('/dashboard/base');
	}
	if (strpos($class, 'BlockController') > 0) {
		$class = substr($class, 0, strpos($class, 'BlockController'));
		$handle = $txt->uncamelcase($class);
		Loader::block($handle);
	} else if (strpos($class, 'Helper') > 0) {
		$class = substr($class, 0, strpos($class, 'Helper'));
		$handle = $txt->uncamelcase($class);
		$handle = preg_replace('/^site_/', '', $handle);
		Loader::helper($handle);
	} else if (strpos($class, 'AttributeType') > 0) {
		$class = substr($class, 0, strpos($class, 'AttributeType'));
		$handle = $txt->uncamelcase($class);
		$at = AttributeType::getByHandle($handle);
	}
}

spl_autoload_register('CoreAutoload', true);