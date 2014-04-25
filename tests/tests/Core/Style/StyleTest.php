<?

class StyleTest extends \PHPUnit_Framework_TestCase {

	/*
	public static function tearDownAfterClass() {
		@unlink(dirname(__FILE__) . '/fixtures/testing.css');
		@unlink(dirname(__FILE__) . '/fixtures/cache/css/testing/styles.css');
		@rmdir(dirname(__FILE__) . '/fixtures/cache/css/testing');
		@rmdir(dirname(__FILE__) . '/fixtures/cache/css');
		@rmdir(dirname(__FILE__) . '/fixtures/cache');

		@unlink(DIR_PACKAGES . '/tester/themes/testerson/styles.css');
		@rmdir(DIR_PACKAGES . '/tester/themes/testerson');
		@rmdir(DIR_PACKAGES . '/tester/themes');
		@rmdir(DIR_PACKAGES . '/tester');	
	}
	*/

	public function testStyles() {
		$definition = dirname(__FILE__) . '/fixtures/styles.xml';
		$styleList = \Concrete\Core\StyleCustomizer\StyleList::loadFromXMLFile($definition);
		$sets = $styleList->getSets();
		$styles = $sets[0]->getStyles();
		$styles2 = $sets[2]->getStyles();

		$this->assertTrue($styleList instanceof \Concrete\Core\StyleCustomizer\StyleList);
		$this->assertTrue(count($styleList->getSets()) == 3);
		$this->assertTrue($sets[2]->getName() == 'Spacing');
		$this->assertTrue($styles[0]->getVariable() == 'background-color');
		$this->assertTrue($styles[1]->getVariable() == 'top-header-bar-color');
		$this->assertTrue($styles[0]->getName() == 'Background');
		$this->assertTrue($styles[1]->getName() == 'Top Header Bar');

		$this->assertTrue($styles[0] instanceof \Concrete\Core\StyleCustomizer\Style\ColorStyle);
		$this->assertTrue($styles2[0] instanceof \Concrete\Core\StyleCustomizer\Style\SizeStyle);

		$this->assertTrue($styles[0]->getFormElementPath() == DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_STYLE_CUSTOMIZER . '/' . DIRNAME_STYLE_CUSTOMIZER_TYPES . '/color.php', sprintf('Incorrect path: %s', $styles[0]->getFormElementPath()));
		$this->assertTrue($styles2[0]->getFormElementPath() == DIR_FILES_ELEMENTS_CORE . '/' . DIRNAME_STYLE_CUSTOMIZER . '/' . DIRNAME_STYLE_CUSTOMIZER_TYPES . '/size.php', sprintf('Incorrect path: %s', $styles2[0]->getFormElementPath()));


	}

}
