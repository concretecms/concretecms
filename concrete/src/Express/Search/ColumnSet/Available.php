<?php
namespace Concrete\Core\Express\Search\ColumnSet;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Search\ColumnSet\Column\AssociationColumn;
use Concrete\Core\Search\Column\Column;

class Available extends DefaultSet
{

    public function getAuthor(Entry $entry)
    {
        $author = $entry->getAuthor();
        if ($author) {
            $ui = $author->getUserInfoObject();
            if ($ui) {
                return $ui->getUserDisplayName();
            }
        }
    }

    public function __construct(ExpressCategory $category)
    {
        parent::__construct($category);

        $this->addColumn(new Column('e.uID', t('Author'), array('\Concrete\Core\Express\Search\ColumnSet\Available', 'getAuthor'), false));

        $associations = $category->getExpressEntity()->getAssociations();
        foreach($associations as $association) {
            $column = new AssociationColumn($association);
            $this->addColumn($column);
        }
    }

}
