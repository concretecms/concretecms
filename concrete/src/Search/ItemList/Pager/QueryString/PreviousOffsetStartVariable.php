<?php
namespace Concrete\Core\Search\ItemList\Pager\QueryString;

class PreviousOffsetStartVariable extends AbstractVariable
{

    public function getVariable()
    {
        return sprintf('ccm_offset_previous_%s', $this->name);
    }


}