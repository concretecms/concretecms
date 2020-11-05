<?php
namespace Concrete\Core\Search\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;

class FileAttributeKeyColumn extends AttributeKeyColumn implements PagerColumnInterface
{

    use AndWhereNotExistsTrait;

    public function getColumnValue($node)
    {
        if (is_object($this->attributeKey)) {
            if ($node->getTreeNodeTypeHandle() == 'file_folder') {
                return '';
            }
            if ($node->getTreeNodeTypeHandle() == 'file') {
                $file = $node->getTreeNodeFileObject();
                if (is_object($file)) {
                    $vo = $file->getAttributeValueObject($this->attributeKey);
                    if (is_object($vo)) {
                        return $vo->getDisplayValue();
                    }
                }
            }
        }
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $db = \Database::connection();
        $value = $db->GetOne('select ' . $this->getColumnKey() . ' from FileSearchIndexAttributes where fID = ?', [$mixed->getFileID()]);
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('(' . $this->getColumnKey() . ', f.fID) %s (:sortColumn, :sortID)', $sort);
        $query->setParameter('sortColumn', $value);
        $query->setParameter('sortID', $mixed->getFileID());
        $this->andWhereNotExists($query, $where);
    }
}
