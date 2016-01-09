<?php

class BlockOverrideTests extends ConcreteTestCase {
    function TemplateTests() {
        $this->UnitTestCase('Block Override Tests');
    }
 	
 	static $_this;
 	
 	/** 
 	 * Gallery package is installed. Can we :
 	 * a. Override gallery/view.php in blocks/gallery.php, properly, as well as maintaining autoloaded items?
 	 * b. Override gallery/view.php in blocks/gallery/view.php. Add new autoload items to /blocks/gallery/ and ensure they are loaded
 	 * c. Setup a custom template in gallery/templates/custom_gallery_template.php, and maintain autoloaded items?
 	 * d. Setup a custom template directory at gallery/templates/custom_gallery_template/view.php, and load NEW autoloaded items?
 	 */
 	public function testOverridePackageViewTemplate() {
 		//assumes /gallery exists
 		$p = Page::getByPath('/gallery');
		
		self::$_this = '';
		self::$_this->path = DIR_REL . '\/packages\/gallery/i';

 		// a.
 		touch(DIR_BASE . '/blocks/gallery.php');
 		
 		$blocks = $p->getBlocks('Main');
 		$b = $blocks[0];
 		$bv = new BlockViewTemplate($b);
		$this->assertEqual($bv->getTemplate(), DIR_BASE . '/blocks/gallery.php', 'Gallery Template Test - Block view override with gallery.php');
		phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php/gallery', array('BlockOverrideTests', '_testGalleryHeaderItems'));	
		$this->assertEqual(3, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Gallery Header Test - Block view override with gallery.php'); 		
 		unlink(DIR_BASE . '/blocks/gallery.php');
 		
 		// b
		self::$_this = '';
		self::$_this->path = DIR_REL . '\/blocks\/gallery/i';
		
		mkdir(DIR_BASE . '/blocks/gallery');
 		touch(DIR_BASE . '/blocks/gallery/view.php');
 		touch(DIR_BASE . '/blocks/gallery/view.css');
 		touch(DIR_BASE . '/blocks/gallery/view.js');
 		
 		$blocks = $p->getBlocks('Main');
 		$b = $blocks[0];
 		$bv = new BlockViewTemplate($b);
		$this->assertEqual($bv->getTemplate(), DIR_BASE . '/blocks/gallery/view.php', 'Gallery Template Test - Block view override with gallery/view.php');
		phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php/gallery', array('BlockOverrideTests', '_testGalleryHeaderItems'));	
		$this->assertEqual(2, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Gallery Header Test - Block view override with gallery/view.php and local view.css, view.js'); 		

 		unlink(DIR_BASE . '/blocks/gallery/view.css');
 		unlink(DIR_BASE . '/blocks/gallery/view.js');
 		unlink(DIR_BASE . '/blocks/gallery/view.php');
		rmdir(DIR_BASE . '/blocks/gallery');
		
		// c
		self::$_this = '';
		self::$_this->path = DIR_REL . '\/packages\/gallery/i';
		
		mkdir(DIR_BASE . '/blocks/gallery');
		mkdir(DIR_BASE . '/blocks/gallery/templates');
 		touch(DIR_BASE . '/blocks/gallery/templates/custom_gallery_template.php');
 		
 		$blocks = $p->getBlocks('Main');
 		$b = $blocks[0];
		$b->setCustomTemplate('custom_gallery_template.php');

 		$p = Page::getByPath('/gallery');
		$blocks = $p->getBlocks('Main');
 		$b = $blocks[0];
 		$bv = new BlockViewTemplate($b);

		$this->assertEqual($bv->getTemplate(), DIR_BASE . '/blocks/gallery/templates/custom_gallery_template.php', 'Gallery Template Test - Custom Template view override with gallery/templates/custom_gallery_template.php');
		phpQuery::browserGet(BASE_URL . DIR_REL . '/index.php/gallery', array('BlockOverrideTests', '_testGalleryHeaderItems'));	
		$this->assertEqual(3, self::$_this->headerItem1Count + self::$_this->headerItem2Count, 'Gallery Header Test - Custom Template view override with gallery/templates/custom_gallery_template.php'); 		

 		unlink(DIR_BASE . '/blocks/gallery/templates/custom_gallery_template.php');
		rmdir(DIR_BASE . '/blocks/gallery/templates');
		rmdir(DIR_BASE . '/blocks/gallery');
		$b->setCustomTemplate(false);
 	}
	
	function _testGalleryHeaderItems($browser) {
		$headerItem1 = $browser->find('link');
		$headerItem2 = $browser->find('script');
		
		foreach($headerItem1 as $hi) {
			$val = $hi->getAttribute('href');
			if (preg_match(self::$_this->path, $val)) {
				//print $val . '<br>';
				self::$_this->headerItem1Count++;
			}
		}

		foreach($headerItem2 as $hi) {
			$val = $hi->getAttribute('src');
			if (preg_match(self::$_this->path, $val)) {
				//print $val . '<br>';
				self::$_this->headerItem2Count++;
			}
		}
	}
}
