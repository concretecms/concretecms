<?php
namespace Concrete\Core\Block;

use Database;

class CacheSettings
{
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputVaryOn = array();
    protected $btCacheBlockOutputVaryOnKey = null;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputLifetime = 0;
    protected $btCacheBlockOutputDynamic = false;
    

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
            $r = $db->GetRow('select * from CollectionVersionBlocksCacheSettings where
              cID = ? and cvID = ? and arHandle = ? and bID = ?',
                array(
                    $cID, $cvID, $arHandle, $bID
                )
            );
            if ($r['bID']) {
                $o = new static();
                $o->btCacheBlockOutput = (bool) $r['btCacheBlockOutput'];
                $o->btCacheBlockOutputDynamic = (bool) $r['btCacheBlockOutputDynamic'];
                $o->btCacheBlockOutputOnPost = (bool) $r['btCacheBlockOutputOnPost'];
                $o->btCacheBlockOutputForRegisteredUsers = (bool) $r['btCacheBlockOutputForRegisteredUsers'];
                $o->btCacheBlockOutputLifetime = $r['btCacheBlockOutputLifetime'];

                $o->btCacheBlockOutputVaryOn = @unserialize($r['btCacheBlockOutputVaryOn']);
                if($o->btCacheBlockOutputVaryOn === false) {
                    $o->btCacheBlockOutputVaryOn = array();
                }
            }
        }
        if (!isset($o)) {
            if ($controller = $b->getController()) {
                $o = new static();
                $o->btCacheBlockOutput = $controller->cacheBlockOutput();
                $o->btCacheBlockOutputDynamic = $controller->cacheBlockOutputDynamic();
                $o->btCacheBlockOutputVaryOn = $controller->cacheBlockOutputVaryOn();
                $o->btCacheBlockOutputOnPost = $controller->cacheBlockOutputOnPost();
                $o->btCacheBlockOutputForRegisteredUsers = $controller->cacheBlockOutputForRegisteredUsers();
                $o->btCacheBlockOutputLifetime = $controller->getBlockTypeCacheOutputLifetime();

            } else {
                $o = new static();
                $o->btCacheBlockOutput = false;
                $o->btCacheBlockOutputDynamic = false;
                $o->btCacheBlockOutputVaryOn = array();
                $o->btCacheBlockOutputOnPost = false;
                $o->btCacheBlockOutputForRegisteredUsers = false;
                $o->btCacheBlockOutputLifetime = false;
            }
        }
        return $o;
    }

    public function cacheBlockOutputVaryOnKey() {

        if($this->btCacheBlockOutputVaryOnKey === null) {
            $varyOnKey = array();
            $req = \Request::getInstance();

            foreach($this->btCacheBlockOutputVaryOn as $field => $settings) {

                if($req->query->has($field)) {

                    if($settings['match']) {
                        if(preg_match($settings['match'],$req->query->get($field),$out)) { 
                            $varyOnKey[$field] = $out[0];
                        } else {
                            $varyOnKey = false;
                            break;
                        }
                    } else {
                        $varyOnKey[$field] = $req->query->get($field);
                    }

                } else if($settings['default']) {
                    $varyOnKey[$field] = $settings['default'];
                }
            }

            if($varyOnKey !== false) {
                $this->btCacheBlockOutputVaryOnKey = md5(serialize($varyOnKey));
            } else {
                $this->btCacheBlockOutputVaryOnKey = false;
            }
        }

        return $this->btCacheBlockOutputVaryOnKey;
    }

    public function cacheBlockOutput()
    {
        return $this->btCacheBlockOutput;
    }

    public function cacheBlockOutputVaryOn()
    {
        return $this->btCacheBlockOutputVaryOn;
    }

    public function cacheBlockOutputOnPost()
    {
        return $this->btCacheBlockOutputOnPost;
    }

    public function cacheBlockOutputForRegisteredUsers()
    {
        return $this->btCacheBlockOutputForRegisteredUsers;
    }

    public function cacheBlockOutputDynamic()
    {
        return $this->btCacheBlockOutputDynamic;
    }

    public function getBlockOutputCacheLifetime()
    {
        return $this->btCacheBlockOutputLifetime;
    }
}
