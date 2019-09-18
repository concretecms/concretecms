<?php
namespace Concrete\Core\Localization\Service;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Punic\Language;

defined('C5_EXECUTE') or die('Access Denied.');

class LanguageList implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * Returns an associative array with the locale code as the key and the translated language name as the value.
     *
     * @return array
     */
    public function getLanguageList()
    {
        $excludeScriptSpecific = true;
        $site = $this->app->make('site')->getActiveSiteForEditing();
        if (is_object($site)) {
            $siteConfig = $site->getConfigRepository();
            $excludeScriptSpecific = !$siteConfig->get('multilingual.support_script_specific_locale');
        }
        $languages = Language::getAll(true, $excludeScriptSpecific);

        return $languages;
    }
}
