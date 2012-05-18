<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	$co = Request::get();
	$include = false;
	if ($co->isIncludeRequest()) {
		$env = Environment::get();
		ob_start();
		switch($co->getIncludeType()) {
			case "CONCRETE_TOOL":
			case "TOOL":
				include($env->getPath(DIRNAME_TOOLS . '/' . $co->getFilename()));
				break;
			case 'PACKAGE_TOOL':
				include($env->getPath(DIRNAME_TOOLS . '/' . $co->getFilename(), $co->getPackageHandle()));
				break;
			case "BLOCK_TOOL":
				if ($co->getBlock() != '') {
					$bt = BlockType::getByHandle($co->getBlock());
					if ($bt->getPackageID() > 0) {
						@include($env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename(), $co->getPackageHandle()));
					} else {
						@include($env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename()));
					}
				}
				break;
		}
		$contents = ob_get_contents();
		ob_end_clean();
		print $contents;

		if (!$contents) {
			header("HTTP/1.0 404 Not Found");
		}

		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}
		
