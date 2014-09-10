<?php defined('C5_EXECUTE') or die('Access Denied.');

define('SITE', Config::get('concrete.site'));

return;
// Things below here need to be searched for and replaced with their new config values.

/**
 * ----------------------------------------------------------------------------
 * User information and registration settings.
 * ----------------------------------------------------------------------------
 */
/** -- Registration -- **/
Config::getOrDefine('USER_REGISTRATION_WITH_EMAIL_ADDRESS', false);
Config::getOrDefine('USER_VALIDATE_EMAIL', false);
Config::getOrDefine('USER_REGISTRATION_APPROVAL_REQUIRED', false);
Config::getOrDefine('REGISTER_NOTIFICATION', false);
Config::getOrDefine('EMAIL_ADDRESS_REGISTER_NOTIFICATION', false);
Config::getOrDefine('REGISTRATION_TYPE', 'disabled');
define('ENABLE_REGISTRATION', REGISTRATION_TYPE != 'disabled');

/** -- Profile settings -- **/
Config::getOrDefine('concrete.misc.user_timezones', false);




/**
 * ----------------------------------------------------------------------------
 * Global permissions and behaviors toggles.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('PERMISSIONS_MODEL', 'simple');
Config::getOrDefine('ENABLE_AREA_LAYOUTS', true);
Config::getOrDefine('ENABLE_CUSTOM_DESIGN', true);
Config::getOrDefine('FORBIDDEN_SHOW_LOGIN', true);
define('PAGE_PERMISSION_IDENTIFIER_USE_PERMISSION_COLLECTION_ID',
    \Concrete\Core\Permission\Access\PageAccess::usePermissionCollectionIDForIdentifier());


/**
 * ----------------------------------------------------------------------------
 * Miscellaneous sitewide settings.
 * ----------------------------------------------------------------------------
 */
Config::getOrDefine('SITE', 'concrete5');
Config::getOrDefine('ENABLE_PROGRESSIVE_PAGE_REINDEX', true);
Config::getOrDefine('MAIL_SEND_METHOD', 'PHP_MAIL');
Config::getOrDefine('SEO_EXCLUDE_WORDS', 'a, an, as, at, before, but, by, for, from, is, in, into, like, of, off, on, onto, per, since, than, the, this, that, to, up, via, with');
