<?php
namespace Concrete\Controller\Search\Express;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Entry\Search\Result\Result;
use Concrete\Core\Express\EntryList;

class Entries extends AbstractController
{
    protected $entryList;
    protected $entity;
    protected $result;
    protected $columnSet;

    public function submit($entityID)
    {
        $em = \Database::connection()->getEntityManager();
        $entity = $em->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($entityID);
        $this->search($entity);
        $this->app->make('helper/ajax')->sendResult($this->result->getJSONObject());
    }

    public function search(Entity $entity)
    {
        $this->entryList = new EntryList($entity);
        $this->entity = $entity;

        $set = $this->entity->getResultColumnSet();
        if ($set) {
            $defaultSortColumn = $set->getDefaultSortColumn();
            if ($this->request->query->has($this->entryList->getQuerySortDirectionParameter())) {
                $direction = $this->request->query->get($this->entryList->getQuerySortDirectionParameter());
            } else {
                $direction = $defaultSortColumn->getColumnDefaultSortDirection();
            }
            if ($this->request->query->has($this->entryList->getQuerySortColumnParameter())) {
                $value = $this->request->query->get($this->entryList->getQuerySortColumnParameter());
                $column = $this->entity->getResultColumnSet();
                $value = $column->getColumnByKey($value);
                if (is_object($value)) {
                    $this->entryList->sanitizedSortBy($value->getColumnKey(), $direction);
                }
            } else {
                $this->entryList->sanitizedSortBy($defaultSortColumn->getColumnKey(), $direction);
            }

            $result = new Result($this->entity->getResultColumnSet(), $this->entryList,
                \URL::to('/ccm/system/search/express/entries/submit', $this->entity->getID()));
            $this->result = $result;
        }
    }

    public function getSearchResultObject()
    {
        return $this->result;
    }

    public function getListObject()
    {
        return $this->entryList;
    }

}
