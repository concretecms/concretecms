<?

class CssTest extends \PHPUnit_Framework_TestCase {

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

	public function testFileCallbackLocator() {
		$l = Core::make("helper/css");
		$dir = realpath(dirname(__FILE__) . '/fixtures/');	
		$l->setLocator(function($file) use ($dir) {
			return $dir . '/' . $file;
		});
		$path = $l->resolveFile('testing.less');
		$this->assertTrue($path == $dir . '/testing.less', sprintf('Path was supposed to be %s, insead it was %s', $dir . '/testing.less', $path));	
	}

	public function testFileEnvironmentCallbackLocator() {
		$l = Core::make("helper/css");
		$env = Environment::get();
		$l->setLocator(function($file) use ($env) {
			$rec = $env->getUncachedRecord(DIRNAME_THEMES . '/greek_yogurt/' . $file, false);
			return $rec->file;
		});
		$path = $l->resolveFile('css/reset.css');
		$this->assertTrue(file_exists($path), sprintf('Attempted to find concrete/themes/greek_yogurt/css/reset.css but path returned as %s', $path));


		mkdir(DIR_PACKAGES . '/tester/themes/testerson/', 0777, true);
		touch(DIR_PACKAGES . '/tester/themes/testerson/styles.css');

		$l->setLocator(function($file) use ($env) {
			$rec = $env->getUncachedRecord(DIRNAME_THEMES . '/testerson/' . $file, 'tester');
			return $rec->file;
		});
		$path = $l->resolveFile('styles.css');
		$this->assertTrue(file_exists($path) && $path == DIR_PACKAGES . '/tester/themes/testerson/styles.css', sprintf('Attempted to find packages/tester/themes/testerson/styles.css but path returned as %s', $path));

	}

	public function testLess() {
		$l = Core::make('helper/css');
		$dir = realpath(dirname(__FILE__) . '/fixtures/');	
		$l->setLocator(function($file) use ($dir) {
			return $dir . '/' . $file;
		});
		$file = $l->less('testing.less');
		$this->assertTrue($file == '/testing.css', sprintf('Supposed to be /testing.css instead it was %s', $file));
		$this->assertTrue(trim(file_get_contents(dirname(__FILE__) . '/fixtures/testing.css')) == 'body a:hover{text-decoration: none}');


		mkdir(dirname(__FILE__) . '/fixtures/cache/css/testing/', 0777, true);
		$l->setCompiledStylesheetOutputPath(dirname(__FILE__) . '/fixtures/cache/css/testing');
		$l->setUrlRootToStylesheet('/my/web/root/cache/css/testing');
		$file = $l->less('styles.less');
		$this->assertTrue($file == '/my/web/root/cache/css/testing/styles.css', sprintf('Supposed to be "/my/web/root/cache/css/testing/styles.css" instead was %s', $file));

		$file = $l->less('styles.less', '/my/web/root');
		$this->assertTrue($file == '/my/web/root/styles.css', sprintf('Supposed to be "/my/web/root/styles.css" instead was %s', $file));

	}

}
