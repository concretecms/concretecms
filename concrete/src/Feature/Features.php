<?php

namespace Concrete\Core\Feature;

/**
 * This is a simple registry class to codify all the core features used by the asset system. Features listed in here
 * are provided in some capacity by the core. Fundamental features like pages, files and users aren't used by the
 * block feature system, because it's assumed the core will always have these available. However, they may be used
 * in the future with a fully modular installation procedure. 
 * 
 * Block types reference these features (i.e. the calendar block declares that it requires the Features::CALENDAR
 * feature), and then themes can specify that they support those features. If they support them, the fallback assets
 * shipped with the core won't be loaded, which will speed up page render time and allow for more flexible themes. If
 * a theme doesn't support a given feature that is used on a page, the fallback assets will load, ensuring that
 * the block still works.
 */
class Features
{

    /**
     * Base, Frontend, backend and CMS features. Essentially mandatory
     */

    const PAGES = 'pages';
    const THEMES = 'themes';
    const FILES = 'files';
    const USERS = 'users';
    const BASICS = 'basics';
    const PERMISSIONS = 'permissions';
    const ATTRIBUTES = 'attributes';

    /**
     * Base, frontend, backend and CMS features that are not mandatory.
     */

    const STACKS = 'stacks';
    const EXPRESS = 'express';
    const CALENDAR = 'calendar';
    const BOARDS = 'boards';
    const NAVIGATION = 'navigation';
    const DOCUMENTS = 'documents';
    const SOCIAL = 'social';
    const FORMS = 'forms';
    const MULTILINGUAL = 'multilingual';
    const VIDEO = 'video';
    const IMAGERY = 'imagery';
    const TAXONOMY = 'taxonomy';
    const TESTIMONIALS = 'testimonials';
    const CONVERSATIONS = 'conversations';
    const FAQ = 'faq';
    const SEARCH = 'search';
    const STAGING = 'staging';
    const MAPS = 'maps';
    const POLLS = 'polls';
    const ACCORDIONS = 'accordions';
    const ACCOUNT = 'account';
    const DESKTOP = 'desktop';
    const PROFILE = 'profile';
    const SUMMARY = 'summary';

    /**
     * Frontend features
     */
    const TYPOGRAPHY = 'typography';

    /**
     * Backend features
     */
    const PACKAGES = 'packages';
    const MULTISITE = 'multisite';
    const MARKETPLACE = 'marketplace';
    const HEALTH = 'health';
    const AUTOMATION = 'automation';
    const NOTIFICATION = 'notification';
    const THEME_DOCUMENTATION = 'theme_documentation';
    const MAIL = 'mail';
    const API = 'api';

    public static function getFeatures(): array
    {
        $reflectionClass = new \ReflectionClass(self::class);
        return $reflectionClass->getConstants();
    }

    public static function getDisplayName(string $feature)
    {
        switch ($feature) {
            case self::PAGES:
                return t('Pages');
            case self::FILES:
                return t('Files');
            case self::USERS:
                return t('Users and Groups');
            case self::STACKS:
                return t('Stacks and Content Library');
            case self::EXPRESS:
                return t('Express Data Objects');
            case self::PACKAGES:
                return t('Packages and Extensions');
            case self::BASICS:
                return t('Basics');
            case self::TYPOGRAPHY:
                return t('Typography');
            case self::CALENDAR:
                return t('Calendars and Events');
            case self::BOARDS:
                return t('Boards');
            case self::NAVIGATION:
                return t('Navigation');
            case self::DOCUMENTS:
                return t('Document Management');
            case self::SOCIAL:
                return t('Social Networking and Sharing');
            case self::FORMS:
                return t('Forms');
            case self::MULTILINGUAL:
                return t('Multilingual Support');
            case self::MULTISITE:
                return t('Multiple Site Hosting');
            case self::VIDEO:
                return t('Video');
            case self::IMAGERY:
                return t('Galleries, Image Sliders and Lightboxes');
            case self::MARKETPLACE:
                return t('Marketplace Connectivity');
            case self::TAXONOMY:
                return t('Tags and Taxonomy');
            case self::TESTIMONIALS:
                return t('Testimonials');
            case self::CONVERSATIONS:
                return t('Conversations');
            case self::FAQ:
                return t('Frequently Asked Questions');
            case self::SEARCH:
                return t('Search and SEO');
            case self::STAGING:
                return t('Development, Staging and Production');
            case self::MAPS:
                return t('Maps');
            case self::AUTOMATION:
                return t('Tasks and Automation');
            case self::POLLS:
                return t('Polls and Surveys');
            case self::ACCORDIONS:
                return t('Accordions');
            case self::ACCOUNT:
                return t('Personal My Account');
            case self::DESKTOP:
                return t('Welcome Desktops');
            case self::PROFILE:
                return t('Public User Profile');
            case self::HEALTH:
                return t('Site Health Reports');
            case self::NOTIFICATION:
                return t('Notification');
            case self::ATTRIBUTES:
                return t('Attribute keys, entities and categories');
            case self::PERMISSIONS:
                return t('Permission keys, entities and categories');
            case self::MAIL:
                return t('Mail');
            case self::THEME_DOCUMENTATION:
                return t('Theme Documentation');
            case self::SUMMARY:
                return t('Summary Templates');
            case self::API:
                return t('API');
        }
    }
}
