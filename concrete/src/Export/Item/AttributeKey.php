<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class AttributeKey implements ItemInterface
{

    public function export(ExportableInterface $key, \SimpleXMLElement $xml)
    {
        /**
         * @var $key Key
         */
        $type = $key->getAttributeType()->getAttributeTypeHandle();
        $categoryHandle = $key->getAttributeKeyCategoryHandle();
        $akey = $xml->addChild('attributekey');
        $akey->addAttribute('handle', $key->getAttributeKeyHandle());

        // @TODO If we're exporting attribute sets we don't include these items.
        // Add in a good way to disable them when running through attribute set export.

        $akey->addAttribute('name', $key->getAttributeKeyName());
        $akey->addAttribute('package', $key->getPackageHandle());
        $akey->addAttribute('searchable', $key->isAttributeKeySearchable());
        $akey->addAttribute('indexed', $key->isAttributeKeySearchable());
        $akey->addAttribute('type', $type);
        $akey->addAttribute('category', $categoryHandle);

        $key->getController()->exportKey($akey);
        return $akey;
    }

}
