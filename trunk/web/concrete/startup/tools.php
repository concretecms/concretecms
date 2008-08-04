<?
	$co = ConcreteRequest::get();
	if ($co->isIncludeRequest()) {
		switch($co->getIncludeType()) {
			case "CONCRETE_TOOL":
				if (file_exists(DIR_FILES_TOOLS_REQUIRED . '/' . $co->getFilename())) {
					include(DIR_FILES_TOOLS_REQUIRED . '/' .  $co->getFilename());
				}
				break;
			case "TOOL":
				if (file_exists(DIR_FILES_TOOLS . '/' . $co->getFilename())) {
					include(DIR_FILES_TOOLS . '/' . $co->getFilename());
				}
				break;
			case "BLOCK_TOOL":
				if ($co->getBlock() != '') {
					$bt = BlockType::getByHandle($co->getBlock());
					if ($bt->getPackageID() > 0) {
						$file = DIR_PACKAGES . '/' . $bt->getPackageHandle() . '/' . DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename();
						if (file_exists($file)) {
							include($file);
						}
					} else if (file_exists(DIR_FILES_BLOCK_TYPES . '/' . $co->getBlock() . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename())) {
						include(DIR_FILES_BLOCK_TYPES . '/' . $co->getBlock()  . '/' . DIRNAME_BLOCK_TOOLS .'/' . $co->getFilename());
					} else if (file_exists(DIR_FILES_BLOCK_TYPES_CORE . '/' . $co->getBlock()  . '/' . DIRNAME_BLOCK_TOOLS . '/' . $co->getFilename())) {
						include(DIR_FILES_BLOCK_TYPES_CORE . '/' . $co->getBlock()  . '/' . DIRNAME_BLOCK_TOOLS .'/' . $co->getFilename());
					}
				}
				break;
		}
		require(DIR_BASE_CORE . '/startup/shutdown.php');
		exit;
	}
		
