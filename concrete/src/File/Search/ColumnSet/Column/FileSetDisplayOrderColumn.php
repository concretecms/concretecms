<?php
namespace Concrete\Core\File\Search\ColumnSet\Column;

use Concrete\Core\Database\Query\AndWhereNotExistsTrait;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Support\Facade\Application;

class FileSetDisplayOrderColumn extends Column implements PagerColumnInterface
{
    use AndWhereNotExistsTrait;

    private $setID;

    public function __construct(Set $set)
    {
        $this->setID = $set->getFileSetID();
        parent::__construct('fsf' . $this->setID . '.fsDisplayOrder', t('Display Order'));
    }

    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $order = Application::getFacadeApplication()->make('database')->connection()->fetchColumn(
            'SELECT fsDisplayOrder FROM FileSetFiles WHERE fsID = ? AND fID = ?',
            [$this->setID, $mixed->getFileID()]
        );
        if ($order === false) {
            return;
        }

        /** @var FileList $itemList */
        $query = $itemList->getQueryObject();
        $query->setParameter('fileSetCursorOrder', $order);
        $query->setParameter('fileSetCursorID', $mixed->getFileID());
        $comparison = $this->getColumnSortDirection() === 'desc' ? '<' : '>';
        $this->andWhereNotExists($query, sprintf(
            '(%s, f.fID) %s (:fileSetCursorOrder, :fileSetCursorID)',
            $this->getColumnKey(),
            $comparison
        ));
    }
}
