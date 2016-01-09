<?php

abstract class ConcreteBlockTest extends ConcreteTestCase {

	// list all methods that blocks are required to pass
	

	// A block can be edited correctly
	public function testBlockEdit() {
	
	}
	
	public function testBlockEditWhenLocalDirectoryExists() {
	
	}
	
	/*
    function testCoreBlocksAddTemplatesForOverrideProblems() {
    	// make all directories in the local space
    	$f = Loader::helper('file');
    	$contents = $f->getDirectoryContents(DIR_FILES_BLOCK_TYPES_CORE);
    	foreach($contents as $con) {
    		mkdir(DIR_FILES_BLOCK_TYPES . '/' . $con);
    		$bt = BlockType::getByHandle($con);
    		
    		global $a, $ap, $c, $cp;
    		if (is_object($bt)) {
				ob_start();
				$bv = new BlockView();
				$bv->render($bt, 'add', array('a' => $a, 'ap' => $ap, 'c' => $c, 'cp' => $cp));
				ob_end_clean();
			}
    	}   	
    	
    	foreach($contents as $con) {
    		rmdir(DIR_FILES_BLOCK_TYPES . '/' . $con);
    	}
    	
    	$this->pass();
    }
	*/
}