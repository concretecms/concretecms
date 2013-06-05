<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	$co = Request::get();
	$include = false;
	if ($co->isIncludeRequest()) {
		$env = Environment::get();
		switch($co->getIncludeType()) {
			case "CONCRETE_TOOL":
			case "TOOL":
				$r = $env->getPath(DIRNAME_TOOLS . '/' . $co->getFilename());
				break;
			case 'PACKAGE_TOOL':
				$r = $env->getPath(DIRNAME_TOOLS . '/' . $co->getFilename(), $co->getPackageHandle());
				break;
			case "BLOCK_TOOL":
				if ($co->getBlock() != '') {
					$bt = BlockType::getByHandle($co->getBlock());
					if ($bt->getPackageID() > 0) {
						$r = $env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename(), $bt->getPackageHandle());
					} else {
						$r = $env->getPath(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename());
					}
				}
				break;
		}
		
		if (file_exists($r)) {
			include($r);
		}


		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}
		
