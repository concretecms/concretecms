<?php

function CoreAutoload($class) {
	$txt = Loader::helper('text');
	if ($class == 'PageList') {
		Loader::model("page_list");
	}
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
	} else if (strpos($class, 'WorkflowHistoryEntry') > 0) {
		$class = substr($class, 0, strpos($class, 'WorkflowHistoryEntry'));
		$handle = $txt->uncamelcase($class);
		$wt = WorkflowType::getByHandle($handle);
	} else if (strpos($class, 'WorkflowRequest') > 0) {
		$class = substr($class, 0, strpos($class, 'WorkflowRequest'));
		$identifier = $txt->uncamelcase($class);
		$lastslash = strrpos($identifier, '_') + 1;
		$category = substr($identifier, $lastslash);
		$request = substr($identifier, 0, $lastslash - 1);
		if ($category && $request) {
			Loader::model('workflow/request/categories/' . $category);
			Loader::model('workflow/request/requests/' . $request);
		}
	}
}

spl_autoload_register('CoreAutoload', true);