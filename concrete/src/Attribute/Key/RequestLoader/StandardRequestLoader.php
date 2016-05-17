<?php
namespace Concrete\Core\Attribute\Key\RequestLoader;

use Concrete\Core\Entity\Attribute\Key\Key;
use Symfony\Component\HttpFoundation\Request;

class StandardRequestLoader implements RequestLoaderInterface
{
    public function load(Key $key, Request $request)
    {
        $key->setAttributeKeyName($request->request->get('akName'));
        $key->setAttributeKeyHandle($request->request->get('akHandle'));
        $key->setIsAttributeKeyContentIndexed((bool) $request->request->get('akIsSearchableIndexed'));
        $key->setIsAttributeKeySearchable((bool) $request->request->get('akIsSearchable'));

        return $key;
    }
}
