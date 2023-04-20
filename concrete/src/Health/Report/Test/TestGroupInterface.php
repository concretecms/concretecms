<?php
namespace Concrete\Core\Health\Report\Test;

interface TestGroupInterface
{

    /**
     * @return TestInterface[]
     */
    public function getTests(): iterable;

}
