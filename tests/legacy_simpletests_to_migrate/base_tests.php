<?php

class BaseTest extends UnitTestCase
{
    public function BaseTest()
    {
        $this->UnitTestCase('Base File and Environment Tests');
    }

    public function testConfigFileExists()
    {
        return $this->assertTrue(file_exists(DIR_BASE . '/config/site.php'));
    }
}
