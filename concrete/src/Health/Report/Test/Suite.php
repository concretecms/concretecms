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
        foreach ($this->items as $item) {
            $object = app($item);
            if ($object instanceof TestInterface) {
                $tests[] = $object;
            } else if ($object instanceof SuiteInterface) {
                foreach ($object->getTests() as $itemTest) {
                    $tests[] = $itemTest;
                }
            }
        }
        return $tests;
    }
}
