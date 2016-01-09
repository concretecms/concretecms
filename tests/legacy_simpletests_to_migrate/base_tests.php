<?php

class BaseTest extends UnitTestCase {
    function BaseTest() {
        $this->UnitTestCase('Base File and Environment Tests');
    }
 
    function testConfigFileExists() {
		return $this->assertTrue(file_exists(DIR_BASE . '/config/site.php'));
    }
}