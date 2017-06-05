<?php
namespace Concrete\Core\Search\ItemList\Pager\QueryString;

class OffsetStartVariable extends AbstractVariable
{

    public function getVariable()
    {
        return sprintf('ccm_offset_start_%s', $this->name);
    }


}