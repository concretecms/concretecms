<?php

namespace Concrete\Core\Controller\Traits;

use Symfony\Component\HttpFoundation\ParameterBag;

trait DashboardBulkUpdaterTrait
{

    protected $canEdit;
    protected $items = null;

    abstract protected function getObjectFromRequestId(string $id);
    abstract protected function canPerformOperationOnObject($object): bool;

    protected function populateItemsFromRequest(): bool
    {
        if (!isset($this->items)) {
            if (is_array($_REQUEST['item'] ?? null)) {
                foreach ($_REQUEST['item'] as $id) {
                    $object = $this->getObjectFromRequestId($id);
                    if ($object) {
                        $this->items[] = $object;
                    }
                }
            }
        }

        if ($this->items === null) {
            $this->items = [];
        }

        if (count($this->items) > 0) {
            $this->canEdit = true;
            foreach ($this->items as $object) {
                if (!$this->canPerformOperationOnObject($object)) {
                    $this->canEdit = false;
                }
            }
        } else {
            $this->canEdit = false;
        }
        return $this->canEdit;
    }

    protected function canAccess()
    {
        $this->populateItemsFromRequest();
        return $this->canEdit;
    }


}
