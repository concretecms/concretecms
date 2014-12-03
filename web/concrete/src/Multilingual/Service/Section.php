<?php

namespace Concrete\Multilingual\Service;

defined('C5_EXECUTE') or die("Access Denied.");

class Section
{

    protected $lang = false;
    protected $section = false;

    /**
     * if no argument is specified, returns the first portion of the current page's path
     * ex: path is /en/about/contact-us this function would return 'en'
     * if $s argument (a string page path) is specified this function tests to see if the current page is within the path
     * @param string $s
     * @return string | boolean
     */
    public function section($s = false)
    {
        if (!$this->section) {
            $ms = MultilingualSection::getCurrentSection();
            /*
            $c = Page::getCurrentPage();
            $cparts = explode('/', $c->getCollectionPath());
            $this->section = $cparts[1];
            */
            if ($ms instanceof MultilingualSection) {
                $this->section = $ms->getCollectionPath();
                $this->section = (strlen($this->section) ? $this->section : ""); // acount for home page
            }
        }
        if ($s == false) {
            return $this->section;
        } else {
            return $s == $this->section;
        }
    }


    public function getSectionByLocale($locale = null)
    {
        if (!strlen($locale)) {
            $locale = self::getLocale();
        }
        return MultilingualSection::getByLocale($locale);
    }

    /**
     * returns the current language
     * @return string
     * @deprecated
     */
    public function getLanguage()
    {
        return self::getLocale();
    }

    /**
     * returns if the current locale is a right-to-left language
     * @return boolean
     */
    public function isRtlLocale()
    {
        $lang = self::getLocale();
        $direction = Zend_Locale_Data::getList($lang, 'layout', 'characters');
        return ($direction['characters'] == 'right-to-left');
    }

    /**
     * gets the locale string for the current page
     * based first on path within the site (section) or session if not available
     * @return string
     */
    public function getLocale()
    {
        $ms = MultilingualSection::getCurrentSection();
        if (is_object($ms)) {
            $lang = $ms->getLocale();
        } else {
            $lang = Loader::helper('default_language', 'multilingual')->getSessionDefaultLocale();
        }
        $_SESSION['DEFAULT_LOCALE'] = (string)$lang;
        return (string)$lang;
    }
}