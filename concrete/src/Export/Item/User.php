<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\User\UserInfo;

defined('C5_EXECUTE') or die("Access Denied.");

class User implements ItemInterface
{

    /**
     * @param $set UserInfo
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     */
    public function export($user, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('user');

        // basic information
        $node->addAttribute('username', $user->getUserName());
        $node->addAttribute('email', $user->getUserEmail());
        if (!$user->isActive()) {
            $node->addAttribute('inactive', 1);
        }
        if (!$user->isValidated()) {
            $node->addAttribute('unvalidated', 1);
        }
        if ($timezone = $user->getUserObject()->getUserTimezone()) {
            $node->addAttribute('timezone', $timezone);
        }

        if ($language = $user->getUserDefaultLanguage()) {
            $node->addAttribute('language', $language);
        }

        // attributes


        // groups
        return $node;
    }

}