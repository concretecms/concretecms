<?php

namespace Concrete\Tests\Asset;

use AssetList;
use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\JavascriptAsset;
use Concrete\Core\Asset\JavascriptImportmapAsset;
use Concrete\Core\Asset\JavascriptModuleAsset;
use Concrete\Tests\TestCase;

class JavascriptAssetTest extends TestCase
{
    public function testAssetsExtendFromJavascriptAsset()
    {
        $module = new JavascriptModuleAsset();
        $importmap = new JavascriptImportmapAsset();
        $this->assertInstanceOf(JavascriptAsset::class, $module);
        $this->assertInstanceOf(JavascriptAsset::class, $importmap);
    }

    public function testCanRegisterModule()
    {
        $al = AssetList::getInstance();
        $al->register('javascript-module', 'test-module', 'js/path/to/test/module.js');
        $asset = $al->getAsset('javascript-module', 'test-module');

        $this->assertInstanceOf(JavascriptModuleAsset::class, $asset);
        $this->assertTrue($asset->isAssetLocal());
        $this->assertEquals('javascript-module', $asset->getAssetType());
        $this->assertRegExp('%^/path/to/server/concrete/js/path/to/test/module.js\?ccm_nocache=[A-Fa-f0-9]+$%', $asset->getAssetURL());
    }

    public function testCanRegisterImportmap()
    {
        $al = AssetList::getInstance();
        $al->register('javascript-importmap', 'test-importmap', '{"imports":{"key":"value"}}');
        $asset = $al->getAsset('javascript-importmap', 'test-importmap');

        $this->assertInstanceOf(JavascriptImportmapAsset::class, $asset);
        $this->assertFalse($asset->isAssetLocal());
        $this->assertEquals('javascript-importmap', $asset->getAssetType());
        $this->assertEquals('{"imports":{"key":"value"}}', $asset->getAssetURL());
    }

    public function testCanRegisterModuleFromPackage()
    {
        $al = AssetList::getInstance();
        $pkg = new \Concrete\Core\Entity\Package();
        $pkg->setPackageHandle('testing_package');
        $pkg->setPackageVersion('1.2.3');
        $al->register(
            'javascript-module',
            'testing/moduletest',
            'js/test.bundle.js',
            ['version' => '1.2.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false],
            $pkg
        );

        $asset = $al->getAsset('javascript-module', 'testing/moduletest');

        $this->assertInstanceOf(JavascriptModuleAsset::class, $asset);
        $this->assertTrue($asset->isAssetLocal());
        $this->assertEquals('javascript-module', $asset->getAssetType());
        $this->assertRegExp('%^/path/to/server/packages/testing_package/js/test.bundle.js\?ccm_nocache=[A-Fa-f0-9]+$%', $asset->getAssetURL());
    }

    public function testCanRegisterImportmapFromPackage()
    {
        $al = AssetList::getInstance();
        $pkg = new \Concrete\Core\Entity\Package();
        $pkg->setPackageHandle('testing_package');
        $pkg->setPackageVersion('1.2.3');
        $al->register(
            'javascript-importmap',
            'testing/importmap',
            '{"imports":{"key":"value"}}',
            ['version' => '1.2.3', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false],
            $pkg
        );

        $asset = $al->getAsset('javascript-importmap', 'testing/importmap');

        $this->assertInstanceOf(JavascriptImportmapAsset::class, $asset);
        $this->assertFalse($asset->isAssetLocal());
        $this->assertEquals('javascript-importmap', $asset->getAssetType());
        $this->assertEquals('{"imports":{"key":"value"}}', $asset->getAssetURL());
    }

    public function testImportmapCanBeJsonEncodedArray()
    {
        $importmap = [
            'imports' => [
                'key' => 'value',
            ],
        ];
        $al = AssetList::getInstance();
        $al->register('javascript-importmap', 'test-importmap', json_encode($importmap));
        $asset = $al->getAsset('javascript-importmap', 'test-importmap');

        $this->assertInstanceOf(JavascriptImportmapAsset::class, $asset);
        $this->assertFalse($asset->isAssetLocal());
        $this->assertEquals('javascript-importmap', $asset->getAssetType());
        $this->assertEquals('{"imports":{"key":"value"}}', $asset->getAssetURL());
    }

    public function testJavascriptModuleOutputIsModule()
    {
        $al = AssetList::getInstance();
        $al->register('javascript-module', 'test-module', 'js/path/to/test/module.js');
        $asset = $al->getAsset('javascript-module', 'test-module');

        ob_start();
        echo $asset;
        $output = ob_get_clean();

        $this->assertStringContainsString('type="module"', $output);
    }

    public function testJavascriptImportmapOutputIsImportmap()
    {
        $al = AssetList::getInstance();
        $al->register('javascript-importmap', 'test-importmap', '{"imports":{"key":"value"}}');
        $asset = $al->getAsset('javascript-importmap', 'test-importmap');

        ob_start();
        echo $asset;
        $output = ob_get_clean();

        $this->assertStringContainsString('type="importmap"', $output);
    }
}
