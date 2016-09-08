<?php

namespace Concrete\Tests\Core\Database;

class EntityManagerClassLoaderTest extends \PHPUnit_Framework_TestCase
{     

    public function testApplicationEntityClasses()
    {
        $root = dirname(DIR_BASE_CORE . '../');
        mkdir($root . '/application/src/Entity/Advertisement', 0777, true);
        copy(dirname(__FILE__) . '/fixtures/BannerAd.php', $root . '/application/src/Entity/Advertisement/BannerAd.php');

        $classExists = class_exists('Application\Entity\Advertisement\BannerAd');

        unlink($root . '/application/src/Entity/Advertisement/BannerAd.php');
        rmdir($root . '/application/src/Entity/Advertisement');
        rmdir($root . '/application/src/Entity');

        $this->assertTrue($classExists);
    }

}