<?php

namespace Concrete\Core\Block;

use Database;

class CacheSettings
{
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputOnEditMode = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputLifetime = 0;

    public static function get(Block $b)
    {
        $o = null;
        if ($b->overrideBlockTypeCacheSettings()) {
            $c = $b->getBlockCollectionObject();
            $cID = $c->getCollectionID();
            $cvID = $c->getVersionID();
            $a = $b->getBlockAreaObject();
            $arHandle = $a->getAreaHandle();
            if ($a->isGlobalArea()) {
                // then we need to check against the global area name. We currently have the wrong area handle passed in
                $arHandle = STACKS_AREA_NAME;
            }

            $bID = $b->getBlockID();
            $db = Database::get();
            $r = $db->fetchAssoc('select * from CollectionVersionBlocksCacheSettings where
              cID = ? and cvID = ? and arHandle = ? and bID = ?',
                [
                    $cID, $cvID, $arHandle, $bID,
                ]
            );
            if ($r) {
                $o = new static();
                $o->btCacheBlockOutput = (bool) $r['btCacheBlockOutput'];
                $o->btCacheBlockOutputOnPost = (bool) $r['btCacheBlockOutputOnPost'];
                $o->btCacheBlockOutputForRegisteredUsers = (bool) $r['btCacheBlockOutputForRegisteredUsers'];
                $o->btCacheBlockOutputOnEditMode = (bool) $r['btCacheBlockOutputForRegisteredUsers']; // Not a typo, use same value for edit mode
                $o->btCacheBlockOutputLifetime = $r['btCacheBlockOutputLifetime'];
            }
        }
        if (!isset($o)) {
            if ($controller = $b->getController()) {
                $o = new static();
                $o->btCacheBlockOutput = $controller->cacheBlockOutput();
                $o->btCacheBlockOutputOnPost = $controller->cacheBlockOutputOnPost();
                $o->btCacheBlockOutputForRegisteredUsers = $controller->cacheBlockOutputForRegisteredUsers();
                $o->btCacheBlockOutputOnEditMode = $controller->cacheBlockOutputOnEditMode();
                $o->btCacheBlockOutputLifetime = $controller->getBlockTypeCacheOutputLifetime();
            } else {
                $o = new static();
                $o->btCacheBlockOutput = false;
                $o->btCacheBlockOutputOnPost = false;
                $o->btCacheBlockOutputForRegisteredUsers = false;
                $o->btCacheBlockOutputOnEditMode = false;
                $o->btCacheBlockOutputLifetime = false;
            }
        }

        return $o;
    }

    public function cacheBlockOutput()
    {
        return $this->btCacheBlockOutput;
    }

    public function cacheBlockOutputOnPost()
    {
        return $this->btCacheBlockOutputOnPost;
    }

    public function cacheBlockOutputForRegisteredUsers()
    {
        return $this->btCacheBlockOutputForRegisteredUsers;
    }

    public function cacheBlockOutputOnEditMode()
    {
        return $this->btCacheBlockOutputOnEditMode;
    }

    public function getBlockOutputCacheLifetime()
    {
        return $this->btCacheBlockOutputLifetime;
    }
}
