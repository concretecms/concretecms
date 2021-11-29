<?php
namespace Concrete\Core\Express\Search\Column;

use Concrete\Core\Express\Search\ColumnSet\Column\AssociationColumn as BaseAssociationColumn;
/**
 * @deprecated
 * Use Concrete\Core\Express\Search\ColumnSet\Column\AssociationColumn instead
 * This class exists because it is serialized in some contexts in 8.5.x and earlier, and so needs to exist
 * in both locations
 */
class AssociationColumn extends BaseAssociationColumn
{


}
