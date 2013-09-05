<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	$co = Request::get();
	$include = false;
	if ($co->isIncludeRequest()) {

		if (isset($_REQUEST['cID'])) {
			$_c = Page::getByID($_REQUEST['cID']);
			$co->setCurrentPage($_c);
		}

		$env = Environment::get();
		$tv = new ToolRequestView();
		$tv->render($co);
	}

		
