<?php
namespace Concrete\Core\Health\Report\Test;

use Concrete\Core\Health\Report\Runner;

interface TestInterface
{


    public function run(Runner $runner): void;

}
