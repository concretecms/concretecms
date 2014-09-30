<?php
namespace Concrete\Core\Block;

use Database;

class CacheSettings
{

    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputLifetime = 0;

    public static function get($cID, $cvID, $arHandle, $bID)
    {
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
            return $o;
        }
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
