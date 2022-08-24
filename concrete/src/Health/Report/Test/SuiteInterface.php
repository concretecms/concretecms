<?php
namespace Concrete\Core\Health\Report\Test;

interface SuiteInterface
{

    /**
     * @return TestInterface[]
     */
    public function getTests(): array;

}
