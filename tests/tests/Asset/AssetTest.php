<?php

use Concrete\Core\Asset\Asset;

class AssetTest extends PHPUnit_Framework_TestCase
{
    public function testPackageAssetURLs()
    {
        $al = AssetList::getInstance();
        $al->register(
            'css', 'test-css', 'css/awesome.css'
        );

        $al->register('javascript', 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', array('local' => false));

        $pkg = new Package();
        $pkg->pkgHandle = 'testing_package';
        $al->register(
            'javascript', 'testing/tab', 'blocks/testing_block/js/tab.js',
            array('version' => '3.2.0', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false), $pkg
        );

        $asset1 = $al->getAsset('css', 'test-css');
        $asset2 = $al->getAsset('javascript', 'testing/tab');
        $asset3 = $al->getAsset('javascript', 'jquery');
        $this->assertEquals('/path/to/server/concrete/css/awesome.css', $asset1->getAssetURL());
        $this->assertEquals('/path/to/server/packages/testing_package/blocks/testing_block/js/tab.js', $asset2->getAssetURL());
        $this->assertTrue($asset2->isAssetLocal());
        $this->assertFalse($asset3->isAssetLocal());
        $this->assertEquals('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', $asset3->getAssetURL());

        // overrides test
        $al->register('javascript', 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.0/jquery.min.js', array('local' => false, 'version' => '2.0'));
        $asset3 = $al->getAsset('javascript', 'jquery');
        $this->assertEquals('//ajax.googleapis.com/ajax/libs/jquery/2.0/jquery.min.js', $asset3->getAssetURL());
    }
}
