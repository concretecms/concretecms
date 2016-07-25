<?php

namespace Concrete\Core\Support\Less;

class TreeCallColor extends \Less_Tree_Call
{

    public function getArguments()
    {
        return $this->args;
    }

    public function getName()
    {
        return $this->name;
    }

    public static function fromTreeCall(\Less_Tree_call $call)
    {
        return new static($call->name, $call->args, $call->index, $call->currentFileInfo);
    }

}
