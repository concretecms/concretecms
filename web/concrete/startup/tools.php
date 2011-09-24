<?
	defined('C5_EXECUTE') or die("Access Denied.");
	
	$co = Request::get();
	$include = false;
	if ($co->isIncludeRequest()) {
		switch($co->getIncludeType()) {
			case "CONCRETE_TOOL":
				if (file_exists(DIR_FILES_TOOLS . '/' . $co->getFilename())) {
					include(DIR_FILES_TOOLS . '/' . $co->getFilename());
					$include = true;
				} else if (file_exists(DIR_FILES_TOOLS_REQUIRED . '/' . $co->getFilename())) {
					include(DIR_FILES_TOOLS_REQUIRED . '/' .  $co->getFilename());
					$include = true;
				}
				break;
			case "TOOL":
				if (file_exists(DIR_FILES_TOOLS . '/' . $co->getFilename())) {
					include(DIR_FILES_TOOLS . '/' . $co->getFilename());
					$include = true;
				}
				break;
			case 'PACKAGE_TOOL':
				if ($co->getPackageHandle() != '') {
					
					$file1 = DIR_FILES_TOOLS . '/' . $co->getPackageHandle() . '/' . $co->getFilename();
					$file2 = DIR_PACKAGES . '/' . $co->getPackageHandle() . '/' . DIRNAME_TOOLS . '/' . $co->getFilename();
					$file3 = DIR_PACKAGES_CORE . '/' . $co->getPackageHandle() . '/' . DIRNAME_TOOLS . '/' . $co->getFilename();
					
					//echo var_dump($file1, $file2, $file3); exit;
					
					if (file_exists($file1)) {
						include($file1);
						$include = true;
					} else if (file_exists($file2)) {
						include($file2);
						$include = true;
					} elseif(file_exists($file3)) {
						include($file3);
						$include = true;
					} 
				}
				break;
			case "BLOCK_TOOL":
				if ($co->getBlock() != '') {
					$bt = BlockType::getByHandle($co->getBlock());
					if ($bt->getPackageID() > 0) {
						$file1 = DIR_PACKAGES . '/' . $bt->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename();
						$file2 = DIR_PACKAGES_CORE . '/' . $bt->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename();
						if (file_exists($file1)) {
							include($file1);
							$include = true;
						} else if (file_exists($file2)) {
							include($file2);
							$include = true;
						}
					} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $co->getBlock() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename())) {
						include(DIR_FILES_BLOCK_TYPES . '/' . $co->getBlock()  . '/' . DIRNAME_BLOCK_TOOLS .'/' . $co->getFilename());
						$include = true;
					} else if (file_exists(DIR_FILES_BLOCK_TYPES_CORE . '/' . $co->getBlock()  . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename())) {
						include(DIR_FILES_BLOCK_TYPES_CORE . '/' . $co->getBlock()  . '/' . DIRNAME_BLOCK_TOOLS .'/' . $co->getFilename());
						$include = true;
					}
				}
				break;
		}
		
		if (!$include) {
			header("HTTP/1.0 404 Not Found");
		}
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}
		
