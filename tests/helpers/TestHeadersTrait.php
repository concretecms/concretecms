<?php

namespace Concrete\TestHelpers;

trait TestHeadersTrait
{
    /**
     * @throws \PHPUnit_Framework_SkippedTestError
     */
    protected function skipIfHeadersSent()
    {
        if (headers_sent($file, $line)) {
            $this->markTestSkipped("This test cannot run once headers have been sent (headers sent on {$file}:{$line}.");
        }
    }
}
