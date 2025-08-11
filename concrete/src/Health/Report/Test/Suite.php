<?php
namespace Concrete\Core\Health\Report\Test;

abstract class Suite implements SuiteInterface
{

    /**
     * @param $className
     */
    public function add($mixed)
    {
        $this->items[] = $mixed;
    }

    /**
     * @var TestInterface[]
     */
    protected $items = [];

    public function getTests(): array
    {
        $tests = [];
        foreach ($this->items as $object) {
            if (is_string($object)) {
                $object = app($object);
            }
            if ($object instanceof TestInterface) {
                $tests[] = $object;
            } else if ($object instanceof TestGroupInterface) {
                foreach ($object->getTests() as $itemTest) {
                    $tests[] = $itemTest;
                }
            }
        }
        return $tests;
    }
}
