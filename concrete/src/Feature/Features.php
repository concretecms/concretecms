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
     * Fundamental feature: Pages
     */
    const PAGES = 'pages';

    /**
     * Fundamental feature: support for uploaded files and the file manager.
     */
    const FILES = 'files';

    /**
     * Fundamental feature: support for users, authentication.
     */
    const USERS = 'users';

    /**
     * Fundamental feature: support for stacks
     */    
    const STACKS = 'stacks';

    /**
     * Fundamental feature: support for Express
     */
    const EXPRESS = 'express';

    /**
     * Fundamental feature: support for package installation.
     */
    const PACKAGES = 'packages';

    /**
     * Fundamental feature: support for basics on the front-end: file block, feature block, content, etc...
     */
    const BASICS = 'basics';

    /**
     * Bedrock typography support: titles, display titles, buttons, colors, etc...
     */
    const TYPOGRAPHY = 'typography';

    /**
     * Accessory feature: support for calendars
     */
    const CALENDAR = 'calendar';
    
    /**
     * Fundamental feature: support for Boards
     */
    const BOARDS = 'boards';

    /**
     * Accessory feature: support for autonav, page list, etc...
     */
    const NAVIGATION = 'navigation';

    /**
     * Accessory feature: support for documents (public file manager)
     */
    const DOCUMENTS = 'documents';

    /**
     * Accessory feature: support for social networking
     */
    const SOCIAL = 'social';

    /**
     * Accessory UI feature: form/data entry interfaces
     */
    const FORMS = 'forms';

    /**
     * Accessory feature: support for multilingual. Note, this does not mean the ability to run concrete5 in a language
     * other than English. This is more about running a site with multiple language trees.
     */
    const MULTILINGUAL = 'multilingual';
    
    /**
     * Accessory feature: support for video
     */
    const VIDEO = 'video';

    /**
     * Accessory feature: support for rich imagery (lightbox, galleries, sliders)
     */
    const IMAGERY = 'imagery';

    /**
     * Accessory feature: support for the concrete5 marketplace.
     */
    const MARKETPLACE = 'marketplace';

    /**
     * Accessory feature: support for tags, topics.
     */
    const TAXONOMY = 'taxonomy';

    /**
     * Accessory feature: support for testimonials
     */
    const TESTIMONIALS = 'testimonials';

    /**
     * Accessory feature: support for conversations
     */
    const CONVERSATIONS = 'conversations';

    /**
     * Accessory feature: support for FAQs
     */
    const FAQ = 'faq';

    /**
     * Accessory feature: support for search
     */
    const SEARCH = 'search';

    /**
     * Accessory feature: support for maps
     */
    const MAPS = 'maps';

    /**
     * Accessory feature: support for surveys
     */
    const POLLS = 'polls';

    /**
     * Accessory feature: support for accordions
     */
    const ACCORDIONS = 'accordions';

    /**
     * Accessory feature: support for My Account/Edit Profile
     */
    const ACCOUNT = 'account';

    /**
     * Accessory feature: support for desktop functionality, like "Waiting for Me", "Draft List", etc...
     */
    const DESKTOP = 'desktop';

    /**
     * Accessory feature: support for frontend user profiles
     */
    const PROFILE = 'profile';


}
