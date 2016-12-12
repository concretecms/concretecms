<?php
namespace Concrete\Core\Page;

class EditResponse extends \Concrete\Core\Application\EditResponse
{
    protected $cID = 0;
    protected $cIDs = [];

    public function setPage(Page $page)
    {
        $this->cID = $page->getCollectionID();
    }

    public function setPages($pages)
    {
        foreach ($pages as $c) {
            $this->cIDs[] = $c->getCollectionID();
        }
    }

    public function getJSONObject()
    {
        $o = parent::getBaseJSONObject();
        if ($this->cID > 0) {
            $o->cID = $this->cID;
        } elseif (count($this->cIDs) > 0) {
            foreach ($this->cIDs as $cID) {
                $o->cID[] = $cID;
            }
        }
        if (isset($o->cID)) {
            if (!is_array($o->cID)) {
                $o->pages[] = Page::getByID($o->cID)->getJSONObject();
            } else {
                foreach ($o->cID as $cID) {
                    $o->pages[] = Page::getByID($cID)->getJSONObject();
                }
            }
        }

        return $o;
    }
}
