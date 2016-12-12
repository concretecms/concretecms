<?php

namespace Concrete\Core\Gathering\Item;

use Core;
use Database;
use Concrete\Core\Gathering\DataSource\DataSource as GatheringDataSource;
use Exception;
use Permissions;

class Page extends Item
{
    public function canViewGatheringItem()
    {
        $cp = new Permissions($this->page);

        return $cp->canViewPage();
    }

    public static function getListByItem($mixed)
    {
        $ags = GatheringDataSource::getByHandle('page');
        if (is_object($ags)) {
            return Item::getListByKey($ags, $mixed->getCollectionID());
        } else {
            return array();
        }
    }

    public static function add(GatheringDataSourceConfiguration $configuration, Page $c)
    {
        $gathering = $configuration->getGatheringObject();
        try {
            // we wrap this in a try because it MIGHT fail if it's a duplicate
            $item = parent::create($gathering, $configuration->getGatheringDataSourceObject(), $c->getCollectionDatePublic(), $c->getCollectionName(), $c->getCollectionID());
        } catch (Exception $e) {
        }

        if (is_object($item)) {
            $db = Database::connection();
            $db->executeQuery(
                'insert into gaPage (gaiID, cID) values (?, ?)',
                array(
                    $item->getGatheringItemID(),
                    $c->getCollectionID(),
                )
            );
            $item->assignFeatureAssignments($c);
            $item->setAutomaticGatheringItemTemplate();

            return $item;
        }
    }

    public function assignFeatureAssignments($c)
    {
        $this->addFeatureAssignment('title', $c->getCollectionName());
        $this->addFeatureAssignment('date_time', $c->getCollectionDatePublic());
        $this->addFeatureAssignment('link', Core::make('helper/navigation')->getLinkToCollection($c));
        if ($c->getCollectionDescription() != '') {
            $this->addFeatureAssignment('description', $c->getCollectionDescription());
        }
        if ($c->getAttribute('is_featured')) {
            $this->addFeatureAssignment('featured', 1);
        }
        $assignments = $c->getFeatureAssignments();
        foreach ($assignments as $fa) {
            $this->copyFeatureAssignment($fa);
        }
    }

    public function duplicate(Gathering $gathering)
    {
        $item = parent::duplicate($gathering);
        $db = Database::connection();
        $db->executeQuery(
            'delete from gaPage where gaiID = ?',
            array($item->getGatheringItemID())
        );
        $db->executeQuery(
            'insert into gaPage (gaiID, cID) values (?, ?)',
            array(
                $item->getGatheringItemID(),
                $this->page->getCollectionID(),
            )
        );

        return $item;
    }

    public function delete()
    {
        parent::delete();
        $db = Database::connection();
        $db->executeQuery('delete from gaPage where gaiID = ?', array($this->getGatheringItemID()));
    }

    public function loadDetails()
    {
        $db = Database::connection();
        $row = $db->GetRow('select cID from gaPage where gaiID = ?', array($this->getGatheringItemID()));
        $this->setPropertiesFromArray($row);
        $this->page = self::getByID($row['cID'], 'ACTIVE');
    }

    public function getCollectionObject()
    {
        return $this->page;
    }
}
