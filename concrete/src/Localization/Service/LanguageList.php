<?php
namespace Concrete\Core\Localization\Service;

use Punic\Language;

defined('C5_EXECUTE') or die('Access Denied.');

class LanguageList
{
    /**
     * Returns an associative array with the locale code as the key and the translated language name as the value.
     *
     * @return array
     */
    public function getLanguageList()
    {
        $languages = Language::getAll(true, true);

        return $languages;
    }
}
