<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Search\ItemList\ItemList;

interface FieldInterface extends \JsonSerializable
{

    public function getKey();
    public function getDisplayName();
    public function renderSearchField();
    public function filterList(ItemList $list, $request);

}
