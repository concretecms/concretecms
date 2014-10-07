<?php
namespace Concrete\Core\Block;

use Database;

class CacheSettings
{

    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputLifetime = 0;

    public static function get(Block $b)
    {
        if ($b->overrideBlockTypeCacheSettings()) {
            $c = $b->getBlockCollectionObject();
            $cID = $c->getCollectionID();
            $cvID = $c->getVersionID();
            $arHandle = $b->getAreaHandle();
            $bID = $b->getBlockID();


            $db = Database::get();
            $r = $db->GetRow('select * from CollectionVersionBlocksCacheSettings where
              cID = ? and cvID = ? and arHandle = ? and bID = ?',
                array(
                    $cID, $cvID, $arHandle, $bID
                )
            );

            if ($r['bID']) {
                $o = new static();
                $o->btCacheBlockOutput = (bool) $r['btCacheBlockOutput'];
                $o->btCacheBlockOutputOnPost = (bool) $r['btCacheBlockOutputOnPost'];
                $o->btCacheBlockOutputForRegisteredUsers = (bool) $r['btCacheBlockOutputForRegisteredUsers'];
                $o->btCacheBlockOutputLifetime = $r['btCacheBlockOutputLifetime'];
            }
        } else if ($controller = $b->getController()) {
            $o = new static();
            $o->btCacheBlockOutput = $controller->cacheBlockOutput();
            $o->btCacheBlockOutputOnPost = $controller->cacheBlockOutputOnPost();
            $o->btCacheBlockOutputForRegisteredUsers = $controller->cacheBlockOutputForRegisteredUsers();
            $o->btCacheBlockOutputLifetime = $controller->getBlockTypeCacheOutputLifetime();
        } else {
            $o = new static();
            $o->btCacheBlockOutput = false;
            $o->btCacheBlockOutputOnPost = false;
            $o->btCacheBlockOutputForRegisteredUsers = false;
            $o->btCacheBlockOutputLifetime = false;
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

    public function getBlockOutputCacheLifetime()
    {
        return $this->btCacheBlockOutputLifetime;
    }

}
