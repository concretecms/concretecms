<?php

class PageTest extends PHPUnit_Framework_TestCase {

	private static function createPage($name, $handle=null) {
		Loader::model('page');
		Loader::model('collection_types');
		$ct = CollectionType::getByHandle('left_sidebar'); //everything's got a default..
		//$this->assertInstanceOf('CollectionType', $ct); //kind of weird to check this but hey

		$home = Page::getByID(HOME_CID);
		$page = $home->add($ct,array(
			'uID'=>1,
			'cName'=>$name,
			'cHandle'=>$handle
		));
		return $page;
	}

	public function testPageOperations() {
		Loader::model('page');
		Loader::model('collection_types');
		$ct = CollectionType::getByHandle('left_sidebar'); //everything's got a default..
		$this->assertInstanceOf('CollectionType', $ct); //kind of weird to check this but hey

		$home = Page::getByID(HOME_CID);
		$pageName = "My Cool Page";
		$pageHandle = 'page'; //this tests that page handles will be set as the page handle.
			//The actual add function does some transforms on the handles if they are not
			//set.
		
		$badPage = Page::getByID(42069);
		try {
			$page = $badPage->add($ct,array(
				'uID'=>1,
				'cName'=>$pageName,
				'cHandle'=>$pageHandle
			));
		} catch(Exception $e) {
			$caught = true;
		}

		if(!$caught) {
			$this->fail('Added a page to a non-page');
		}

		$page = self::createPage($pageName,$pageHandle);

		$parentID = $page->getCollectionParentID();

		$this->assertInstanceOf('Page',$page);
		$this->assertEquals($parentID, HOME_CID);

		$this->assertSame($pageName,$page->getCollectionName());
		$this->assertSame($pageHandle, $page->getCollectionHandle());
		$this->assertSame('/'.$pageHandle, $page->getCollectionPath());
		//now we know adding pages works.

		$destination = self::createPage("Destination");

		$parentCID = $destination->getCollectionID();

		$page->move($destination);
		$parentPath = $destination->getCollectionPath();
		$handle = $page->getCollectionHandle();
		$path = $page->getCollectionPath();

		$this->assertSame($parentPath.'/'.$handle, $path);
		$this->assertSame($parentCID, $page->getCollectionParentID());
		//now we know that moving pages works

		$page->moveToTrash();
		$this->assertTrue($page->isInTrash());
		//stuff is going to the trash

		$cID = $page->getCollectionID();
		$page->delete();
		$noPage = Page::getByID($cID);
		$this->assertEquals(COLLECTION_NOT_FOUND,$noPage->error); //maybe there is a more certain way to determine this.
		//now we know deleting pages works

		$destination->delete();
		//clean up the destination page
	}

	/**
	 *  @dataProvider pageNames
	 */
	public function testPageNames($name, $special) {
		$page = self::createPage($name);
		$parentID = $page->getCollectionParentID();
		$this->assertSame($page->getCollectionName(), $name);
		$th = Loader::helper('text');
		if(!$special) {
			$this->assertSame($page->getCollectionPath(), '/'.$th->urlify($name));
			$this->assertSame($page->getCollectionHandle(), $th->urlify($name));
		} else {
			$this->assertSame($page->getCollectionPath(), '/'.(string)$page->getCollectionID());
			$this->assertSame($page->getCollectionHandle(), '');
		}
		$page->delete();
	}

	public function pageNames() {
		return array(
			array('normal page',false),
			array("awesome page's #spring_break98 !!1! SO COOL",false),
			array('niño borracho',false),
			array('雷鶏',true)
		);
	}

	public function testPageDuplicate() {
		$page = self::createPage('double vision');
		$home = Page::getByID(HOME_CID);

		$newPage = $page->duplicate($home);
		$realNewPage = Page::getByID($newPage->getCollectionID(),'ACTIVE');

		$this->assertNotEquals($page->getCollectionID(),$realNewPage->getCollectionID());
		$this->assertEquals($page->getCollectionPath().'-2',$realNewPage->getCollectionPath());
		$this->assertEquals($page->getCollectionName().' 2',$realNewPage->getCollectionName());

		$page->delete();
		$realNewPage->delete();
	}

}
