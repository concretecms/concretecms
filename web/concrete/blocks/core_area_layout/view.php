<?php
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();

	$container = $formatter->getLayoutContainerHtmlObject();
	foreach($columns as $column) {
		$html = $column->getColumnHtmlObject();
		$container->appendChild($html);
	}
	print $container;
