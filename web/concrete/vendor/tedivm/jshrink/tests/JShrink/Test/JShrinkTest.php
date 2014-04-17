<?php

/*
 * This file is part of the JShrink package.
 *
 * (c) Robert Hafner <tedivm@tedivm.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JShrink\Test;

use JShrink\Minifier;


class JShrinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider JShrinkProvider
     */
    public function testJShrink($testName, $unminified, $minified) {
        $this->assertEquals(\JShrink\Minifier::minify($unminified), $minified, 'Running JShrink Test: ' . $testName);
    }

    /**
     * @dataProvider uglifyProvider
     */
    public function testUglify($testName, $unminified, $minified) {
        $this->assertEquals(\JShrink\Minifier::minify($unminified), $minified, 'Running Uglify Test: ' . $testName);
    }


    public function getExampleFiles($group)
    {
        $baseDir = __DIR__ . '/../../Resources/' . $group . '/';
        $testDir = $baseDir . 'test/';
        $expectDir = $baseDir . 'expect/';

        $returnData = array();


        $testFiles = scandir($testDir);
        foreach($testFiles as $testFile)
        {
            if(!file_exists(($expectDir . $testFile)))
                continue;

            $testContents = file_get_contents($testDir . $testFile);
            $testResults = file_get_contents($expectDir . $testFile);

            $returnData[] = array($testFile, $testContents, $testResults);
        }

        return $returnData;
    }


    public function uglifyProvider() {
        return $this->getExampleFiles('uglify');
    }

    public function JShrinkProvider() {
        return $this->getExampleFiles('jshrink');
    }



}