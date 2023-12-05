# 9.2.3

## New Features

## Behavioral Improvements

* Renamed Twitter to “X” in the social networking and social sharing services.
* Health: add a link from reports to the "Start a New Report" page (thanks mlocati)
* Logs with long paths in their messages no longer display beneath the Dashboard panel in the Logs report.
* Packages are now alphabetically sorted in the Dashboard listing interface (thanks JohnTheFish)
* Add the package name and version to the package install success message (thanks JohnTheFish)
* Translate package name in update message (thanks JohnTheFish)

## Bug Fixes

* Fixed error when saving a layout preset under PHP 8.
* Fixed importing IP access log channels (thanks mlocati)
* Fixed issue when importing trees and tree nodes when used with custom classes in packages.
* Fixed: we export three custom styles for blocks and areas that we don’t import (thanks mlocati)
* Fixed bug where if a file folder was added as a favorited and then deleted in the file manager the user would receive errors when using the file chooser.
* Fixed weird behavior when using the content exporter to export pages with scrapbook pasted blocks in them (thanks mlocati)
* Fixed importing RSS displayer blocks under certain conditions from CIF XML (thanks mlocati)
* Bug fixes to CIF XML files (thanks mlocati)
* Fixed: Topic List block: Add missing titleFormat to exported CIF (thanks mlocati)
* Bug fixes to importing tree node types (thanks mlocati)
* Bug fixes to importing site type skeletons (thanks mlocati)
* Fix bug in c5:translate –fill (thanks mlocati)
* Bug fixes to editing page types under PHP 8 in certain conditions (thanks mlocati)

## Developer Notes

* The X social networking service icon is provided as an SVG - meaning that your theme may need to be updated to properly style SVGs as well as font icons when displaying “Share this Page” or “Social Networking” service icons.
* Cleanup of CIF XML files (thanks mlocati)
* Improvements to the Xml service class (thanks mlocati)
* We now accept boolean-like values when importing booleans from CIF XML files (thanks mlocati)

## Security Fixes

* Fixed [CVE-2023-44762](https://nvd.nist.gov/vuln/detail/CVE-2023-44762) Reflected XSS in Tags with [commit 11764](https://github.com/concretecms/concretecms/pull/11764/commits/af57186f2a9d6d8f597e4580f8b2ad05c7f5a609) This vulnerability only affects only Concrete version 9.2 through 9.2.2 since the file this touches is in Bedrock, using a custom library the project wrote for version 9.2.0.
* Fixed [CVE-2023-44764](https://nvd.nist.gov/vuln/detail/CVE-2023-48651) Stored XSS in Concrete Site Installation in Name parameter with [commit 11764](https://github.com/concretecms/concretecms/pull/11764).
* Fixed [CVE-2023-48652](https://nvd.nist.gov/vuln/detail/CVE-2023-48652) Cross Site Request Forgery (CSRF) via /ccm/system/dialogs/logs/delete_all/submit with [commit 11764]( https://github.com/concretecms/concretecms/pull/11764/commits/747d5bf776cc81c708f5d8f0d64a1e7eafb3f218) An attacker can force an admin user to delete server report logs on a web application to which they are currently authenticated. The Concrete CMS Security team scored this 6.3 with CVSS v3 vector [AV:N/AC:L/PR:N/UI:R/S:U/C:L/I:L/A:L](https://nvd.nist.gov/vuln-metrics/cvss/v3-calculator?vector=AV:N/AC:L/PR:N/UI:R/S:U/C:L/I:L/A:L&version=3.1). This does not affect versions below 9. Thanks Veshraj Ghimire for reporting. 
* Fixed [CVE-2023-48651](https://nvd.nist.gov/vuln/detail/CVE-2023-48651) by updating Update Dialog endpoints to only accept Post requests with tokens included with [commit 11764]( https://github.com/concretecms/concretecms/pull/11764/commits/747d5bf776cc81c708f5d8f0d64a1e7eafb3f218) Prior to fix Cross Site Request Forgery (CSRF) to delete files vulnerability is present at /ccm/system/dialogs/file/delete/1/submit. The Concrete CMS Security team scored this 4.3 with CVSS v3 vector [AV:N/AC:L/PR:N/UI:R/S:U/C:N/I:N/A:L](https://nvd.nist.gov/vuln-metrics/cvss/v3-calculator?vector=AV:N/AC:L/PR:N/UI:R/S:U/C:N/I:N/A:L&version=3.1) This does not affect versions below 9. Thanks Veshraj Ghimire for reporting. 
* Fixed [CVE-2023-48653](https://nvd.nist.gov/vuln/detail/CVE-2023-48653) Cross Site Request Forgery (CSRF) via ccm/calendar/dialogs/event/delete/submit by updating Dialog endpoints to only accept Post requests with tokens included with [commit 11764]( https://github.com/concretecms/concretecms/pull/11764/commits/747d5bf776cc81c708f5d8f0d64a1e7eafb3f218) for 9.2.3. Prior to fix, an attacker can force an admin to delete events on the site because the event ID is numeric and sequential. The Concrete CMS Security team scored this 4.3 with CVSS v3 vector [AV:N/AC:L/PR:N/UI:R/S:U/C:N/I:L/A:N](https://nvd.nist.gov/vuln-metrics/cvss/v3-calculator?vector=AV:N/AC:L/PR:N/UI:R/S:U/C:N/I:L/A:N&version=3.1) Thanks Veshraj Ghimire for reporting. 
* Fixed [CVE-2023-48650](https://nvd.nist.gov/vuln/detail/CVE-2023-48650) Stored XSS in Layout Preset Name with [commit 11764](https://github.com/concretecms/concretecms/pull/11764) in 9.2.3 and [commit 11765](https://github.com/concretecms/concretecms/pull/11765) in 8.5.14. The Concrete CMS Security team scored this 3.5 with CVSS v3 vector [AV:N/AC:L/PR:H/UI:R/S:U/C:L/I:L/A:N](https://nvd.nist.gov/vuln-metrics/cvss/v3-calculator?vector=AV:N/AC:L/PR:H/UI:R/S:U/C:L/I:L/A:N&version=3.1)  Thanks Solar Security CMS Research, with [d0bby](https://hackerone.com/d0bby), [wezery0](https://hackerone.com/wezery0), [silvereniqma](https://hackerone.com/silvereniqma) in collaboration for reporting!
* Fixed [CVE-2023-49337](https://nvd.nist.gov/vuln/detail/CVE-2023-49337) Stored XSS on Admin Dashboard via /dashboard/system/basics/name with [commit 07b4337]( https://github.com/concretecms/concretecms/commit/07b433799b888c4eb854e052ca58b032ebc6d36f) The Concrete CMS Security team scored this 2.4 with CVSS v3 vector [AV:N/AC:L/PR:H/UI:R/S:U/C:N/I:L/A:N](https://nvd.nist.gov/vuln-metrics/cvss/v3-calculator?vector=AV:N/AC:L/PR:H/UI:R/S:U/C:N/I:L/A:N&version=3.1) Thanks Ramshath MM for reporting H1 2232594. This vulnerability is not present in Concrete 8.5 and below. 

# 9.2.2

## New Features

* Added a Switch Language option to the Top Navigation Bar, allowing the navigation bar to present a list of site languages and facilitate switching between them for the given page (thanks hissy)

## Behavioral Improvements

* Express Detail block now has support for getSearchableContent: pages that contain this block will have that block’s content properly added to the search index.
* We now display the minimum and maximum username length when adding users in the Dashboard (thanks ounziw)
* Prevent loading full tree views when not needed, improving performance with large topic trees in topic attributes, large file manager trees on Dashboard user and file manager pages. 
* Add package name and version to the message displayed after a package update (thanks JohnTheFish)
* Improvements to clarity in field layout when resetting a user’s password from the Dashboard (thanks iampedropiedade)
* Page List block outputs canonical path only when ccm_paging_p is 2 or greater (thanks ccmEnlil)
* Site-wide attributes will now be grouped by set if sets have been enabled for site attributes (thanks parasek)
* Added links to the images in the Atomik blog summary templates.
* Updating some automatically created directories to use the proper directory permissions (thanks mlocati)
* Clicking the labels of the checkboxes in the Rich Text Editor Settings Dashboard page will not check the appropriate checkbox (thanks mlocati)

## Bug Fixes

* Fixed bug where page attributes were added to the attribute index immediately upon saving, even if the version they were joined to had not yet been approved. 
* Fixed bug where announcements might not have been displayed to certain users who should see them.
* Fixed bug when using advanced permissions in file manager with File Uploader access entity under certain conditions.
* Fixed bug in the Atomik theme where a board would error if certain properties on a page were not set.
* Fixed bug in advanced permissions that made it impossible to select a custom date/time range for a permission access entity.
* Fixed: Page with gallery block breaks if deletes an image from the File manager.
* jQuery UI is no longer required to use the core date/datetime attribute (thanks hamzaouibacha)
* Fixed: Help block for related topics on page list form is incorrect (thanks ccmEnlil)
* Fixed: Can't delete a user who is favoriting a folder in the file manager (thanks mlocati)
* Fixed error where Page not found after updating URL slug of a page in composer.
* Improved compatibility with PHP 8.2 and greater.
* Fixed: ResponseAssetGroup::requireAsset required "core/rating" but "core/rating" is not a valid asset group handle
* Fixed: Feature Link block: Undefined variable $buttonColor error on PHP8
* Removed directory selector from File manager add file dialog because it could slow things down significantly.
* Fixed bug where certain marketplace files would be marked as incompatible with the current version when they were not actually incompatible under PHP versions lower than 8.
* Fixed Undefined variable $calendarID with PHP 8 when working with calendar boards configuration under PHP 8.
* Fixed bug where Multi-site default site attributes at the Site Type level were not working.
* Fixed: --env command option is ignored on v9 (thanks jscott-rawnet)
* Fixed issue where users who were granted the ability to edit page type drafts were not actually able to publish those drafts.
* Link settings in an image block will now export properly when using the Migration Tool (thanks hissy)
* Fixed issue where if you’re filtering by a topic using custom code, similarly named topics would return objects assigned to both topics (thanks pszostok)
* Fix error when an invalid file is passed into the download file single page (thanks JohnTheFish)
* Fixed bug where nested groups would show HTML for their breadcrumbs when viewed in the user group search in the user advanced search.
* Fixed some instances where the CollectionSearchIndexAttributes table might be updated based on the latest version instead of the approved version (thanks biplobice)
* Fixed concrete/attributes/email/controller.php:33 Undefined array key "value" (thanks mlocati)
* Fixed: PHP 8 deprecation warnings on login page (thanks mlocati)
* Remove HTML from user_group attribute form.
* Prevents PHP8 undefined key exception in Snippet::getByHandle() (thanks bikerdave)
* "Invalid or Empty Node passed to getItem constructor." error on adding express form in certain languages (thanks hissy)
* Bug fixes to the download file page under PHP8 (thanks JohnTheFish)
* Fix error when logging in as another user with multisite enabled under PHP8.
* Fixed Undefined variable $user on /login/session_invalidated under PHP 8 (thanks hissy)
* Fixed bug where certain users may not have been able to dismiss announcements.
* Fixed issue where "Subpage Permissions" setting is ignored when draft pages are inherited from defaults (thanks hissy)
* Add missing t() in "Edit Page List" block view so it can be translated (thanks mlocati)
* Fixed bug when trying to use Calendar summary templates to select a specific sub-set of summary templates as available for a particular event.
* Fixed errors when accessing Express attribute keys programmatically if they had the phrase “get” at any point in them.
* Load fresh version object instead of cached one when running update (thanks pszostok)
* Fixed: Express Form Block's Form Name doesn't get changed after first setting (thanks hamzaouibacha)
* Sanitize the output of the Accordion block title field (thanks ismeashim)
* We now properly sanitize the output of files uploaded through Express Forms. 
* Updated to Guzzle 7.8, remediating INSERT ISSUE HERE!!!
* Updated League OAuth2 Server dependency to 8.4.2 to fix security issue.
* Better sanitization of Plural handles in Express objects.
* Better sanitizing of Custom labels in Express objects.

## Developer Improvements

* Added new capabilities for custom theme documentation pages (pages that use site page types and page templates for support elements, but still live in the documentation pages area.)
* Made ReindexPageCommand fully synchronous, and added a new QueueReindexPageCommand that is asynchronous for use when developers want to queue a page for reindexing asynchronously.

* Added new console command `concrete:theme:activate` and `concrete:theme:activate-skin`.
* Added the ability to affect the new page’s display order and page path when using the on_page_duplicate event.
* Enhance DeleteGroupCommand to customize its handling of sub-groups (thanks mlocati)
* Developers can now override the PageItem and Navigation classes within the Top Navigation Bar using custom code if they choose to do so (thanks danklassen)


## Security Fixes
* Updated the Guzzle HTTP library to 7.8 to ensure Concrete CMS is not vulnerable to Guzzle [CVE-2023-29197](https://nvd.nist.gov/vuln/detail/CVE-2023-29197) Thank you Danilo Costa for reporting H1 2132287
* Fixed <CVE Pending> Directories could be created with insecure permissions since file creation functions gave universal access (0777) to created folders by default. Excessive permissions could be granted when creating a directory with permissions greater than 0755 or when the permissions argument was not specified. The Concrete CMS Security team scored this 6.6 with CVSS v3 vector [AV:N/AC:H/PR:H/UI:N/S:U/C:H/I:H/A:H](https://nvd.nist.gov/vuln-metrics/cvss/v3-calculator?vector=AV:N/AC:H/PR:H/UI:N/S:U/C:H/I:H/A:H&version=3.1) Thanks tahabiyikli-vortex for reporting H12122245. Thanks Mlocati for providing the fix. Fixed in commit [11677](https://github.com/concretecms/concretecms/pull/11677)
* Fixed <CVE Pending> stored XSS on the Concrete Admin page by sanitizing uploaded file 
names. The Concrete CMS Security team scored this 3.5 with CVSS v3 vector [AV:N/AC:L/PR:L/UI:R/S:U/C:L/I:N/A:N](https://nvd.nist.gov/vuln-metrics/cvss/v3-calculator?vector=AV:N/AC:L/PR:L/UI:R/S:U/C:L/I:N/A:N&version=3.1) Thanks @akbar_jafarli for reporting H1 2149479. Fixed in commit [11695](https://github.com/concretecms/concretecms/pull/11695)  
* Fixed [CVE-2023-44761](https://nvd.nist.gov/vuln/detail/CVE-2023-44761) Admin can add XSS via Data Objects with this [commit](https://github.com/concretecms/concretecms/commit/4990e68e8a4be2ee66e2d2bfcbfca38712614f76)
* Fixed [CVE-2023-44765](https://nvd.nist.gov/vuln/detail/CVE-2023-44765) Stored XSS Associations (via data objects) with commit [11746](https://github.com/concretecms/concretecms/pull/11746)

# 9.2.1

## New Features

* Added a thumbnail property to the Feature and Feature Link block types (thanks katalysis)
* File manager image editor now supports full screen mode (thanks mlocati)

## Behavioral Improvements

* Reinstated the ability to attach accounts to external authentication providers on the My Account page.
* Use User->isRegistered() instead of User->isLoggedIn() throughout Concrete (Thanks mlocati)
* Top Navigation Bar now honors `replace_link_with_first_in_nav` custom attribute (thanks danklassen)
* Top Navigation Bar block can now use the site name for branding text if no custom branding text is defined in the block.
* Dashboard image editor is now larger (thanks mlocati)
* Minor display improvements
* First weekday in calendar is now defined by the locale instead of being hard-coded to Sunday (thanks mlocati)
* Page Selector and User Selector attributes now work better when used with Express label entry display masks/labels.
* Image editor in Dashboard now reloads an image detail page when an asset is edited (thanks mlocati)
* Display more details when explaining why a package cannot be installed due to problems in the package controller (thanks mlocati)
* Dashboard File Details page now reloads when versions are changed (thanks mlocati)
* Improved appearance of Express Entry Details block.
* Added optional alphabetical sort to to block type sets using a configuration option (see here: https://github.com/concretecms/concretecms/pull/11292) (thanks mnakalay)
* Dates displayed in Site Health reports are now properly localized (thanks mlocati)
* Logs Dashboard page now reloads when logs are cleared (thanks mlocati)
* Content replacement should be slightly faster when dealing with large amounts of block records.

## Bug Fixes

* Many additional stricter code fixes under PHP 8.2 (thanks mlocati)
* Fixed: Express form with file upload attributes results in multiple copies of a file in the file manager.
* Fixed inability to do Board instance editing of individual slots.
* Fixed inability to view site health reports under certain conditions.
* Fixed bug where selecting “Force file to download” in a block would result in being unable to un-check and save the setting at a later point (thanks mlocati)
* Fixed bug where conversations were not getting a unique ID when being created, leading duplicate conversations when being added.
* Fixed some misnamed migrations (thanks mlocati)
* Bug fixes to redirect response in GenericOauthTypeController (thanks mlocati)
* We now properly pass the type object to the authentication type controllers upon instantiation (thanks mlocati)
* Fixed errors importing files in the incoming directory (thanks JeRoNZ)
* OAuth service provider: avoid deprecated methods, display errors properly (thanks mlocati)
* Fixed bug where adding an attribute to a page via the attributes panel would clear out select attribute options set against that page if they existed.
* Fixed: Using the feature block, if the icon is not selected, an exception occurs with PHP 8.x due to an undefined array index (thanks JeRoNZ)
* Fixed: Can't bulk edit attributes on page search v9.2 (thanks mlocati)
* Fix View pages using a specific block type (thanks mlocati)
* Fixed Social links stacking instead of displaying inline (thanks nikkiklassen)
* Fixed: Health Check - "Consider enabling logging on tasks." incorrect link
* Fixed: If a page doesn't have the tags attribute attached to it but has a Tags Block you will get this error when accessing that page (thanks mlocati)
* Fixed some errors when detaching OAuth2 accounts (thanks mlocati)
* We now properly pass the item object to user interface menu controllers (thanks mlocati)
* Multilingual - Exception when try to reload strings (thanks mlocati)
* Fixed: Fixed attempt to read property "pTemplateID" results in null under some very rare circumstances.
* PHP 8 Fix: Fix warnings when viewing /dashboard/reports/logs (thanks mlocati)
* Fixed error when searching Logs by their severity level in the Dashboard (thanks lemonbrain-mk)
* Fixed bug where Express object added to the API was unavailable in the API if it had been added via the in-page form builder.
* Fix Undefined property error on PHP 8 in WorkflowAccess class (thanks hissy)
* Fixed error when attempting to use the Closure password validator (thanks gregheafield)
* Fix Undefined array key "scheme" in redis drivers (thanks mlocati)
* Fixed inability to revert page to draft (thanks JeRonZ).
* Fixed Feature and Feature Link block types not exporting their files or importing them properly when used with the Migration Tool.
* Fixed: Pages with theme defined preset layouts crash when editing if the theme is changed (thanks JeRoNZ)
* Fix accessing undefined array index in dialog/block/design.php under certain conditions (thanks mlocati)
* Fix ckeditor language path & remove declaration variable $useLanguage (thanks hamzaouibacha)
* Fixed error when using sitemap selector that nodes in the unexpanded areas would not be selected when those areas were expanded (thanks deanL-zuiderlicht)
* Declare width, height and size in ccmi18n_filemanager object is used in ConcreteFileChooser component so it’s properly localized (thanks hamzaouibacha)
* Currently active geolocation library is now properly highlighted. (thanks mlocati)
* When given a list of topic node ID's such as tid:54,tid:56, the method updateAttributeValueFromTextRepresentation() only imports the last ID in the list when importing content (thanks JohnTheFish)

## Developer Improvements

* Fixed: The email validation with the EmailValidator class gets passed even if it contains emojis (thanks biplobice)
* Developers can now define the minimum PHP version required for a Concrete package with the `getPhpVersionRequired` in their package controllers (thanks mlocati)
* Developers can now specify if certain block content fields ought to be run through the content importer replaceContent method, by including them in the `$btExportContentColumns` protected array in their block controller.
* Fix support for `C5_ENVIRONMENT_ONLY` env variable (thanks mlocati)
* Move the `on_user_logout` event at the end of the logout (thanks mlocati)
* Upgrade `primal/color` third party color parsing library for better PHP 8 compatibility (thanks mlocati)
* Add `on_before_user_logout`, enable customization of post-logout URL (thanks mlocati)
* icon-bar class now included in the Navigation fallback asset so themes that the Top Navigatiaon Bar block will support it when using fallback assets.
* Add ability to column at a specific position (thanks biplobice)
* Added new MemoryOutput class for tasks for diagnostic purposes (thanks mlocati)

# 9.2.0

## Major New Features

* Refinements to the in-page editing experience: better highlight of editable blocks and areas, better delineation of containers, layouts and in-page areas, better hit areas for draggable blocks and much more.
* New “Site Health” Hub: run reports against your site to ensure that its optimally configured. Extensible reports engine ships with the ability to check site for production status settings, cache settings, unauthorized JavaScript and more. Learn more at https://www.youtube.com/watch?v=K76xk1E6hPE
* Complete 1.0 REST API with coverage of major Concrete CMS features, including pages, users, files, Express objects and more. 

## New Features

* Added production modes to the Dashboard - tell Concrete whether this copy is in development, staging or production mode. Useful when running security health checks, or automatically displaying a staging notice to admins or visitors on a staging copy of a site.
* Added the ability to view and retry failed queue messages within the Dashboard and through the use of a command line tool. (https://www.loom.com/share/83530934986940b98f74ebe108e49c6e)
* Added a button to clear all running processes in case any get stuck.
* Adds ability to configure Composer form sets to be collapsable (thanks Mesuva)
* Adds option to filter events in Event List by Past, Future or All Events (thanks katalysis)
* Adds option to change sort order by Most Recent First or Oldest First (thanks katalysis)
* Added new password strength meter to user creation and password changing Dashboard pages (thanks shahroq)
* Added new URL Slug Dashboard page to the SEO section, where you can change settings related to URL slugs (thanks hissy)
* We no longer fall back to using the super admin’s email address as the default address if certain specific addresses aren’t set; instead we use a new config value “default email address”, settable in config code and from the Dashboard email options page (Thanks mlocati)
* Added the ability to specify several allowed IP addresses to avoid triggering logout on IP address change. Added user-specific IP address overrides as well (thanks mlocati)
* Improvements to user experience when passwords are reset for users by administrators, either for a single user, or for all users in the site (mlocati). Users will no longer have to enter their email addresses twice, and will no longer be told that they’re in the “forgot password” user flow, when they’re actually in the manual reset user flow.
* Added the ability to force user passwords changes every X days (thanks mlocati)
* Added the ability to mark a password as reset from a Dashboard user detail page (thanks mlocati)
* Add more info in user details dashboard page (thanks mlocati)
* Added a new full page caching setting that determines the lifetime of the page based on the blocks on the page (thanks hissy)
* Defaulted file manager and file manager component in chooser to sorting by name ascending for more consistent behavior.
* New user avatar editor component in My Account and Dashboard.
* Added a config option to disable asciify for uploaded files (thanks hissy)


## Behavioral Improvements

* Improved display of View Page as User panel.
* Using group paths when group operations are logged instead of group names (thanks mlocati)
* Activating the Elemental or Atomik themes after installation will install required supported templates. 
* Added min fields to page list block number fields (thanks ccmEnlil)
* Core guest, registered and admin groups once again forced to be created with the proper initial IDs (thanks mlocati)
* New conversations message notifications now appear in Waiting for Me.
* Top Navigation Bar block now correctly links to the multilingual home pages, and includes nav-path-selected CSS classes on parent pages of active pages.
* Top Navigation Bar now honors nav target custom attribute (thanks ccmEnlil)
* API Integrations can limit which Concrete CMS product areas they cover via custom scopes. 
* Add missing for attribute to checkbox label of option list attribute (thanks Mesuva)
* SMTP config page: don't send the SMTP password to the clients (thanks mlocati)
* Fix UI of "Update Languages" dashboard page (thanks mlocati)
* Heartbeat backend call updates “Online Now” user property (thanks mlocati)
* Add option to disable asciify on generate url slug (thanks hissy)
* Performance improvement: All global areas’' blocks no longer loaded on every page load (thanks mnakalay)
* Fixed: Breadcrumb block doesn't respect replace_link_with_first_in_nav attribute (thanks hissy)
* Fixed error where Express Entry List criteria in the block were being shown twice.
* Changed image slider URL field from textarea to text input for better display and less ability to mess up input by putting in newlines (thanks nikolai-nikolajevic)
* Dashboard Environment Information page now wraps its content properly (thanks JohnTheFish)
* Fixed error where containers when used on page would block that page from engaging in automated full-page caching (thanks hissy)
* Added date/time of previous login to Welcome back dashboard and account screens.
* File title is now included when searching via the file manager file/folder interface.
* Much improved, more uniform appearance to select pickers and combo boxes when using autocomplete functionality.
* Better block caching settings for certain core block types (thanks 
* Added additionally indexes throughout (thanks jlucki)
* Performance Improvement: Avoid getting same attribute values multiple times (thanks hissy)
* Added a new publish notification if a page has a publish end date that is earlier than the current date (and is therefore closed) (thanks hissy)
* Alias pages are no longer included in sitemap.xml.

## Bug Fixes

* Fixed: Express Form Block submission cannot be edited (thanks mnakalay)
* Fixed bug: Viewing versions of a page with permissions does not work
* Fixed bug: Page preview fails if page is protected
* Fixed bug: Unable to view mobile preview, page versions panel detail, custom design before publish the page
* Fixed bug where unapproved conversation messages were being sent to subscribers.
* Fixed bug where advanced search dialogs in the Dashboard weren’t accurately showing default search and sort order selections.
* Add the missing user param on page_version_approve event (thanks chauve-dev)
* Fix sorting results of FolderItemList by file title when only full group by SQL mode is enabled (thanks mlocati)
* Many bug fixes to searchable lists.
* Bug fixes to Tags attribute that fixes inability to remove tags, other problems.
* Fixed: For draft pages, the destination is the Drafts directory if you create the page in another language.
* Fixed inability to use query parameter ccm_order_by broken with block express_entry_list (thanks mnakalay)
* Fixed issue where editing a JPEG using the image editor would save that file with the JPEG extension but the file behind the scenes was actually a PNG.
* Fixed Calendar block not being properly localized.
* Fix issue under PHP8 when saving select/option attributes with no selected values (thanks Mesuva)
* Fixed bug where tag block showing tags on a specific page did not limit properly.
* Fixed /concrete/single_pages/download_file.php:23 Undefined variable $fID under PHP 8.
* Fixed inability to set home folder when editing a user in the Dashboard.
* Fixed: [V9][Bug] Order by FileSet not working in Document Library Block (thanks mnakalay)
* Fixed: "select fileset" dialog in file manager doesn't retain file set values (thanks mnakalay)
* Fixed error registering users with email validation under PHP 8.
* Exporting users now checks the permission of the access user export permission.
* When running validate-schema via the console no more errors are reported (thanks biplobice)
* Fixed errors regarding `titleFormat` in multiple blocks under PHP8
* Fixed error when placing site into maintenance mode.
* Fixed: Dashboard user attributes always required when present and empty even if not required when editing attributes 
* Fixed: If ID of the Home page isn't 1, we can't manage access rights to site
* Image attribute causing js error in composer and attribute panel (thanks mlocati)
* Fixed bug where marking a page description as required in composer made it impossible to approve the page version even when description was specified. 
* Fixed error when hiding username on new registration form under PHP 8.
* Fixed error using layout sliders on non-Bedrock themes.
* Many small errors and code incompatibilities fixed in group notifications (thanks mlocati)
* Fix handling of page removal when deleting a calendar event (thanks mlocati)
* Fixed PHP errors when using Legacy Form block with PHP 8 (thanks mlocati)
* Fixed some exceptions in BlockController when using PHP8 (thanks biplobice)
* Fixed Wrong params order in the call of View::element(), under elements\workflow\edit_type_form_required.php (thanks BSalaeddin)
* Fixed bug where removing orphaned blocks that are part of page defaults for a page template deletes them from all pages of that type (thanks hissy)
* Fixed error when using Check Automated Groups task.
* Fixed error when saving page type order in the Page Type Order and Group Dashboard page under PHP 8 (thanks hissy)
* Fixed error when visiting URL of deleted private message: Undefined property: Concrete\Core\User\PrivateMessage\PrivateMessage::$uID
* Fixed: Tags Block Ignores Display Limit
* Fixed JavaScript error in version 9 themes when using address attributes.
* Fixed: Presets transparent less variable are replaced by colors when upgrading to concrete version 9 (thanks apaccou)
* Fixes in browsers where certain asynchronous operations could result in a popup saying “undefined” when navigating away from a page
* Fixed: Attempting to delete the "social block" gave displayOrder error under PHP 8.1.
* Fixed: Bugfix: Bulk update for page attributes only saves first selected page (thanks lvanstrijland)
* Fixed misnamed spam allowlist parameter that could result in spam allowlist functionality not working for all configurations (thanks gantanikhiliraj)
* Fixed some bugs in conversations under PHP 8.
* Fixed error displaying languages in Dashboard Breadcrumb dropdown on Global Areas Dashboard page when multilingual is enabled.
* Fix undefined array key when exporting Express entries on PHP 8 (thanks JeffPaetkau)
* Fixed: Get an antispam library by handle breaks under PHP 8 (thanks mnakalay)
* Fixed: Undefined variable $selectedTemplate" error on design panel when editing single pages in PHP 8 (thanks hissy)
* Fixed error when a user has no rights to do settings on express, but can edit the entities (under PHP 8) (thanks Lemonbrain)
* Fixed: HTML block breaks composer interface on PHP 8.1 (thanks hissy)
* Fixed Unable to install with MariaDB 10.10+ (thanks mlocati)
* Fixed: Adding Core Property 'Text' to Express Form Causes Error under PHP 8.
* Fixed occasional errors that could occur if a config file is written twice in rapid succession (thanks JohnTheFish)
* Fixes to the user registration email template (thanks jlucki)
* Add cache lock to fix potential race condition with attribute keys (thanks jlucki)
* Fixed: Legacy form dashboard view "Undefined array key ..." under PHP8
* Fixed: Undefined array key "ptComposerOutputControlID" error on page type default page after removing a composer control under PHP 8
* Fixed behavior where if a custom file storage was set as default it was not selected when adding new folders (thanks hissy)
* Document library block forcing download of files outside default storage location (as attachment)


## Backward Compatibility Notes

* The user autocomplete quickSelect method now defaults to showing user avatars and including usernames and email addresses (if the site is configured to use usernames). This is likely desired for an administrative component but if you’re using quickSelect on the frontend you may wish to restrict this behavior. Consider modifying your usage of quickSelect to use the AUTO_MINIMUM constant and enable/disable user avatars as you like.
* Bootstrap Select has been deprecated. It is still shipping with Bedrock but will be removed in a subsequent version update. Update your code to use new Concrete select components instead.
* The encryption service (unused by the core) has been removed; there is no replacement built into the core but many third party libraries are available in packagist.
* The v-date-picker and v-calendar Vue components have been removed. They are attractive but they are simply too large to include in the JavaScript that powers Concrete. They have been replaced with native solutions. It is unlikely that you’ve included these components in custom code, but if you have you’ll need to import them into your JavaScript bundles yourself.
* The vue toggle Vue component has been removed. It was too large to include in the Concrete CMS JavaScript bundle. If you need this functionality use Bootstrap Switches, which are now included and available.

## Developer Updates

* Bedrock updated to 1.4, which includes support for Bootstrap 5.2 and many other updates.
* Numerous minor PHP dependency updates
* New Group selector Vue component (Thanks mlocati)
* New ConcreteSelect, ConcreteUserSelect, ConcretePageSelect and other components.
* Developers can now add to the list of email addresses displayed on the System Email Addresses Dashboard page for their custom add-ons (thanks mlocati)
* Display the php-cs-fixers applied when the phpcs CLI command applies fixes (thanks mlocati)
* FancyTree deprecated errors no longer displayed in Sitemap (thanks mlocati)
* Theme developers may add required additional content XML for their theme in content.xml in the theme root - it will be installed if (and only if) the theme is activated.
* Added an option to hide usernames from the user picker component (thanks mlocati)
* Add the setupSiteInterfaceLocalization in the controller method in ResponseFactory.php (thanks chauve-dev)
* Deprecate Ajax::isAjaxRequest (thanks mlocati)
* Removed more instances of “concrete5” in favor of “Concrete CMS”
*  Guzzle PHP Library updated to 7.5.
* Concrete now supports Doctrine ORM 2.14.x+
* Fixed error when running method `getPermissionObject` from the `BlockController` class.
* Many minor PHP dependency version updates.
* Minor improvements to antispam service (thanks mnakalay)
* Updates to block controller code to future-proof for PHP 9 (thanks mlocati)


* moment.js has been updated to the latest stable version. This file could sometimes trigger insecurity warnings.

## Security Fixes

* Fixed CVE-2023-28477 Stored XSS on API Integrations via name parameter. Prior to fix While adding API Integrations on concrete cms, the parameter name accepted special characters enabling malicious JavaScript payloads impacting /dashboard/system/api/integrations and /dashboard/system/api/integrations/view_client/unique-id. Concrete CMS Security team CVSS scored this 5.5 AV:N/AC:L/PR:H/UI:N/S:U/C:H/I:L/A:N. Thanks Veshraj Ghimire for reporting H1 1753684 and providing the fix.  Fixed in commit 
* Fixed CVE-2023-28476 Stored XSS on Tags. Prior to fix there was no sanitation when adding tags on uploaded files. Concrete CMS Security team scored this 4.5 with CVSS v3.1 AV:N/AC:L/PR:H/UI:R/S:U/C:H/I:N/A:N. Thanks Veshraj Ghimire and Ashim Chapagain for reporting H1#1767949 and providing the fix. Fixed in commit
* Fixed: CVE-2023-28475 Reflected XSS on the Reply form by ensuring msgID is sanitized. Concrete CMS Security team scored this 4.2 with CVSS v3.1 vector AV:N/AC:H/PR:N/UI:R/S:U/C:L/I:L/A:N.   Thanks Bogdan Tiron from Fortbridge for reporting H1 1772092. Fixed in commit #11279
* Fixed CVE-2023-28474 Stored XSS on Saved Preset. Prior to fix, there was no sanitation when saving presets on search. Concrete CMS Security team scored this 3.5 with CVSS v3.1 vector AV:N/AC:L/PR:H/UI:R/S:U/C:L/I:L/A:N Thanks Veshraj Ghimire for reporting H1 1768494 Fixed in commit
* Fixed CVE-2023-28472 Secure and Http only attributes are now set for ccmPoll cookies. Concrete CMS Security team scored this 3.4 with CVSS v3.1 vectorAV:N/AC:H/PR:N/UI:R/S:C/C:L/I:N/A:N Fixed in commit #11000 
* Fixed CVE-2023-28473 possible Auth bypass in the jobs section. Concrete CMS Security team scored this 2.2 with CVSS v3.1 vector AV:N/AC:H/PR:H/UI:N/S:U/C:N/I:L/A:N Thanks Adrian Tiron from Fortbridge for Reporting H1 1772230. Fixed in commit #11118
* Fixed moment.js CVE-2022-24785. Concrete now pulls in updated versions of moment.js  Concrete CMS Security team scored this 2.2 with CVSS v3.1 vector AV:N/AC:H/PR:H/UI:N/S:U/C:N/I:N/A:L Thanks Fortbridge for reporting. Fixed in commit 11085
* Fixed: CVE-2023-28471 XSS on container name. Prior to fix, there was no sanitization on the container name resulting in stored XSS. Concrete CMS Security team scored this 2.0 with CVSS v3.1 vectorAV:N/AC:H/PR:H/UI:R/S:U/C:L/I:N/A:N Thanks Ashim Chapagain for reporting H1: 1866111] and providing Concrete CMS Pull request #11209

# 9.1.3

## Behavioral Improvements

* Made the legacy_salt functionality easier to read

## Security Fixes
See our [security release blog post](https://www.concretecms.org/about/project-news/security/concrete-cms-security-advisory-2022-10-31) for more information about security fixes.

### Medium
* [CVE-2022-43693](https://nvd.nist.gov/vuln/detail/CVE-2022-43693) Added "state" parameter to OAuth client by default to prevent CSRF. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43692](https://nvd.nist.gov/vuln/detail/CVE-2022-43692) Sanitized output to prevent XSS in dashboard search pages. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43694](https://nvd.nist.gov/vuln/detail/CVE-2022-43694) Sanitized output in API endpoint to prevent potential reflected XSS in the Image Manipulation Library. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43967](https://nvd.nist.gov/vuln/detail/CVE-2022-43967) Sanitized output in multilingual dashboard report to prevent reflected XSS. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43968](https://nvd.nist.gov/vuln/detail/CVE-2022-43968) Sanitized output on the icons dashboard page to prevent reflected XSS. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43686](https://nvd.nist.gov/vuln/detail/CVE-2022-43686) Improved performance of "forever" cookie to prevent DOS. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43691](https://nvd.nist.gov/vuln/detail/CVE-2022-43691) Hide `$_SERVER` and `$_ENV` output from whoops by default to prevent information disclosure. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43687](https://nvd.nist.gov/vuln/detail/CVE-2022-43687) Generate a new session ID when authenticating through OAuth to prevent session fixation. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* Sanitized dashboard breadcrumbs to prevent stored XSS. Thanks @_akbar_jafarli_for reporting HackerOne report #1696363.

### Low
* [CVE-2022-43695](https://nvd.nist.gov/vuln/detail/CVE-2022-43695) Sanitized entity names in entity association dashboard page to prevent stored XSS. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43690](https://nvd.nist.gov/vuln/detail/CVE-2022-43690) Use strict comparison when testing against legacy password algorithm to prevent against potential integer conversion. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43688](https://nvd.nist.gov/vuln/detail/CVE-2022-43688) Sanitize Microsoft tile icon to prevent stored XSS. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.
* [CVE-2022-43689](https://nvd.nist.gov/vuln/detail/CVE-2022-43689) Disable entity expansion when sanitizing SVGs to prevent DNS based IP disclosure. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for reporting.


### Not Ranked
* Added a warning for admins when they are potentially giving more access than they expect when they set certain advanced permissions. Thanks Bogdan and Adrian Tiron from FORTBRIDGE for suggesting.
* Added a warning when moving groups that permissions of the new parent group will be granted to the child group but the child group will retain all previous permissions.Thanks Bogdan and Adrian Tiron from FORTBRIDGE for suggesting.

# 9.1.2

## New Features

* Added “Exclude Current Page” option to the Page List block (thanks ccmEnlil)
* Added new “Upload Settings” Dashboard page to configure file upload settings, including chunking, chunk size, and parallel streams (thanks mlocati).

## Behavioral Improvements

* WebP images now supported by the file manager. WebP images will show up with the proper extension and thumbnail (assuming the browser supports them). File extension added to the file manager list view.
* Many minor UI fixes throughout Dashboard pages and edit dialogs (thanks shahroq)
* Improved display of Environment information Dashboard page: larger window of text.
* Removed ability to approve versions of drafts – because they need to be published first.
* If a folder is specified as the root folder of a document library, uploaded files will be placed in this folder if uploaded through the document library.
* Nicer version history view in add-on update screen (thanks biplobice)
* Much improved scrolling of page when dragging blocks into the page using the Atomik theme.
* Fixed weird Chrome behavior where sometimes dialog windows would have a fully opaque black background.
* Added the ability to toggle passwords when adding a user or change your user’s password (thanks shahroq)
* API Integrations Dashboard page now more suitable for situations where many integrations exist. Supports search, pagination, etc…
* Add a pull down menu to set datetime format for CSV exports (thanks hissy)
* Hide username on edit profile when it is not required on registration (thanks hissy)
* Allow for saving Hero Image Blocks without Image while avoiding the current datatype Exception (thanks haeflimi)
* Mercure overhauled to default all Concrete events to private (for better security). 
* Added additional configuration methods to Server-Sent Events (Mercure) to allow for more advanced configuration use cases.
* Fixed display of CMS when wrapping areas in text-align styles.
* Added environment hostname and name to Environment page (thanks shahroq)
* Improvements to Event List block edit dialog.
* Improved display of navigation in the Express Dashboard pages (thanks shahroq)
* Improvements to the Concrete user input component (thanks mlocati)
* By default, login will take you to the home page of your site (this can be changed from the Login Destination Dashboard page, if desired.)

## Bug Fixes

* Fixed bug where automated groups were not working properly.
* Fixed bug where users could not change the custom template of a block in a Stack.
* Fixed custom options forms not showing properly in third party Captcha packages
* Fixed error editing Hero Image block in PHP 8+ when title format had not been set.
* Fixed bugs under PHP 8+ when configuring advanced properties of advanced permissions.
* Fixed: Background Color of a custom skin can no longer be cleared but destroy the custom skin itself
* Fixed: Adding layout throws error in console "Cannot read properties of undefined (reading 'closest')" in v9.1.1
* Fixed display issues and content issues in the Help panel.
* Added some better content in the help panel.
* Fixed bug where Copy languages feature copied all pages instead of only pages that have not been associated.
* Fixed: Setting Atomik Top Navigation Bar Color to transparent breaks theme cusomiser
* Fixed bug in Atomik sample content where blog posts weren’t showing up because they were going in with dates that were too old.
* Fixed bug where only the super user could assign user groups or remove user groups through the bulk editing interface.
* Fix/error in reindex contents task with Page Objects when pages are in the trash/don’t have a public date (thanks deek87)
* Fixed error in breadcrumb block rendering when parent pages were unapproved (thanks hissy)
* Fixed bug where editing block visibility at certain device breakpoints via custom design was not working (thanks deek87)
* Fixed bug where clearing the site’s cache may lead to an error when using custom cache drivers like Redis (thanks chauve-dev)
* Fixed bug where “page topics” filtering option in Event List block didn’t work and didn’t present a list of topics.
* Fixed bug where large images added via the Content block would burst out of the Atomik theme.
* Fixed bug where images saved in the database with UUID placeholders didn’t display properly (can happen when using the migration tool with version 9)
* Fixed bug where calendar block would not display properly on older themes.
* Fixed bug where pages would not validate in the w3c validator due to a closing `</link>` tag being present.
* Fixed error when adding an Event List block where topic attributes were present under PHP 8.1 (thanks TMDesigns)
* Fixed error when changing locale on Multilingual Setup page (thanks jocomail78)
* File upload chunking now works again (if enabled) (thanks mlocati)
* Fixed: “Your Computer” tab initially empty when swapping files in the file manager (thanks mlocati)
* Fixed bug where filtering by topic tree in the Event List block didn’t show a topic tree to choose from.
* Fixed miscellaneous bugs in Event List block edit dialog.
* Fixed ability to edit certain content in the rich text editor in the Accordion block.
* Fixed interaction where adding a layout and then cancelling would hide the area the layout was added to until the page was reloaded.
* Fixed gallery block error where a gallery referencing a deleted image would cause an Exception (thanks JeffPaetkau)
* Fixed: In php 8 when signed in as a non super user an error occurs when accessing the /dashboard/extend/update page due to $mi not being defined (thanks danklassen)
* Fixed dialogs/block/design.php - Line 12 has an extra closing php tag (thanks ConcreteOwl)
* Fixed Back button not taking you anywhere when viewing an Express entry that was owned by another Express entry.
* Fixed bug on Organize page types Dashboard page under PHP 8.1.
* Fixed error adding basic workflow in PHP 8.1.
* Fixed error editing groups under PHP 8 (thanks hissy)
* Fixed "An exception occurred while executing 'insert into CollectionVersionBlocks" when changing page template.
* Fixed: When using PHP8 if you turn Advanced Permissions on then try to add Block Permissions you're met with this error.
* Fixed: Setting nothing to Items Per Page option of Express Entry List causes an error
* Fixed: Incorrect tag namespace for multilingual sitemap generation (thanks gregheafield)
* Fixed: Page Selector Attribute - Search& Indexing broken (thanks haeflimi)
* Bug fixes for Page List block under PHP 8.1 (thanks ccmEnlil)
* Fixed: Express Form Block E-Mail notification doesn't respect form field Order
* Fixed: Express Form Block E-Mail notification – URL to entries doen't work and leads to empty page 
* Fixed error when updating file sets in PHP8+ (thanks ccmEnlil)
* Fixed errors when using Server-Sent events introduced in 9.1.0
* Fixed bug when using magic method in form helper to create previously undefined form input types (thanks JohnTheFish)
* Fixed bug where page list block would offer the number of entries as the rss feed title if the block was being edited.
* Fix LaminasCacheDriver does not set TTL properly (thanks hissy)
* Fixed: Saving Page with Legacy Attribute Error with PHP8
* Fixed ugly styling for authentication when logging in via Oauth2
* Fixed community authentication (community.concretecms.com) - now it works again.

## Backward Compatibility Notes

* Tweaked Auto-Nav block controller to fix issue with Community Store breadcrumb custom template.

## Developer Updates

* Private properties in Select Attribute Controller updated to be protected (thanks biplobice)
* MessageBusManager library improvements for extension
* Update the URL of the Doctrine XML repository/GitHub Pages (thanks mlocati)
* Any custom integrations using Mercure (likely very few, if any) should be checked over – Mercure system has been completed overhauled, including an update to Symfony Mercure 0.61.
* Added `on_get_page_wrapper_class()` custom event to allow developers to customize classes delivered by this method (thanks JohnTheFish)
* Let translators swap file extension and file type (thanks mlocati)
* Added ability to pass class to tabs method (thanks shahroq)
* Form helper __call magic method can now output form types that have dashes in them (thanks mlocati)
* Add an option to the DeleteGroup command to skip deleting groups with users
* Added application/pdf to the types of files that can be used with view_inline (thanks hissy)

# 9.1.1

## Behavioral Improvements

* Enhancement: adding the ability to pass association ID through request and pick it up in the form
* Adding associations to Express form notifications
* Top Navigation Bar block now honors the `nav_target` custom attribute, if it exists (thanks ccmEnlil)

## Bug Fixes

* Fixed bug in /ccm/system/upgrade script on PHP 8.1 (thanks ccmEnlil)
* Fixed upgrade inconsistencies that could cause problems for installers like Softaculous
* Fixed Accordion Block: when the initial state set to 'all items open' or 'all items closed' the collapsed state is not always correct (thanks danklassen)
* Fixed compatibility with PHP 8.1 when installing with Composer.
* Fixing bug where Express entries with multiple associations could not be filtered accurately in advanced search
* Fixing bug where submitted values do not persist in Express association forms
* Fixed: Changing the page template of a draft breaks block versioning (thanks jaromirdalecky)
* Fixed: Duplicating file as non-super admin does not work due to permissions key (thanks danklassen)
* Fixed: core search block: the form tag has two class attributes
* Fixed null pointer Exceptions when using area layouts under certain conditions (thanks biplobice)


## Backward Compatibility Notes

## Developer Updates

* Laminas cache laminas/laminas-cache-storage-adapter-memory library updated to 2.0 in order to restore compatibility with PHP 8.1 when installing via Composer
* Fixed: Block::isOriginal() returns opposite value (thanks jaromirdalecky)

# 9.1.0

## New Features

* Improved appearance and functionality when editing block, area, layout and container styles inline in the page (thanks deek87)
* Added the ability for an Express attribute to be marked as unique, provided its attribute type supports it. Unique attributes will be useful for SKUs, enforcing email uniqueness, etc…
* Much improved version comparison feature that can compare the HTML of two page versions and highlight differences (thanks deek87 and hissy)
* Feature Link block improvements: Adds option for 'link' styled button using BS5 .btn-link button class, Adds the option to include an icon in the button and to have icon only buttons. Moves some construction of the button to the view file to allow easy comprehension/modification/extension in Block Templates by novice developers (thanks Katalysis)
* Hero Image block improvements: Adds option for 'link' styled button using BS5 .btn-link button class, Adds the option to include an icon in the button and to have icon only buttons. Moves some construction of the button to the view file to allow easy comprehension/modification/extension in Block Templates by novice developers (thanks Katalysis)
* Added new Security Policy page in the Dashboard (thanks hissy)
* Added a “Revert to Draft” command button on published pages in the Composer interface (thanks hissy)
* Improvements and refinements to Dashboard file details screen in desktop and mobile views.
* Added the ability to move a file folder in the Dashboard file manager.
* Added the tree view back to the Groups Dashboard page.
* Add title field for YouTube and Video block types for better accessibility (thanks Mesuva)

## Behavioral Improvements

* Express attributes no longer need to be unique across all Express objects. Instead attribute handles can be reused provided they’re not reused within the same object.
* New Express forms will be created when Express Form blocks that have been copied are edited in their new locations (thanks Xanweb)
* File chooser has improved view and functionality; bug fixes; adding width, height and size to list and grid view; adding detail image callout on hover.
* Task Options in the Dashboard have have been moved into a modal dialog when present, so they’re harder to miss (thanks deek87)
* Express entity attribute handles now can be reused as long as they’re not reused within the same Express object.
* You can now click on the entire row of a Dashboard results table (like the page search, file manager, etc…) and go to the detail URL.
* Better display of inline floating commands for things like containers and block move. 
* We now show the container name when hovering over containers in edit mode.
* Reinstated CSS and JavaScript asset post-processing cache setting; restructured the Dashboard Cache Settings page for better grouping of functionality and explanation.
* Improve display of Recaptcha settings page.
* Appearance improvements to Waiting for Me and the Dashboard desktop.
* Active classes for pages added to the output of the Top Navigation Bar block (thanks danklassen)
* Locale home page is now undeleteable when using multilingual sites.
* Miscellaneous performance improvements for logged-in users (thanks hissy)
* Added rate limiting to Forgot Password using the built-in IP Allowlist/Denylist functionality
* Better usage of meta canonical tag in page under certain circumstances (thanks hissy)
* File folders now cannot be deleted if they have sub-folders or sub-files in them.
* Display improvements to inline style dropdown (no more too-dark panels with no contrast.)
* Better automatic display of the “Approve Stack” button when editing block parameters, styles and permissions in the stacks Dashboard page.
* Don’t allow users to delete site types until they have removed all sites of that type.
* Improvements when Concrete is installed in a subdirectory instead of the root directory of a website.
* Added the ability to view a user’s public profile from their Dashboard user details page.
* Added `--session-handler` to the console install utility. Set to `database` if you’d like to override the default file-based sessions.
* Gotten rid of the behavior where certain dynamic trees cause pages to scroll to them on load (visible on Express Object details edit, adding groups, using the Groups selector in custom Dashboard pages, and more)
* JavaScript and CSS assets now have the timestamp of when the cache was last cleared appended to them (thanks deek87, haeflimi)
* Added the link back to the “Data Objects” Express management interface from the header of that Express objects results page.
* Added URL Path as a column that can be added to the Page Search interface.
* Fixed: Login page forces gray background on custom themes
* Fixed: Scheduled page publishing doesn't purge the page cache (thanks hissy)
* Added more caching to certain objects to improve performance (thanks hissy)
* Pre-selected File Storage Location For Nested Folder

## Bug Fixes

* Much improved PHP 8 compatibility fixes for all core block types (thanks deek87)
* Fixed user permissions for searching users with non super admin not working in sites upgraded from 8.5 until permissions were reset.
* Fixed inability to assign groups, users, group sets or group combinations to group permissions when updating from 8.5.
* Improvements to core libraries to allow for installation on PHP 8.1 w/Composer.
* PHP 8 compatibility fixes for Calendar (thanks deek87)
* Fixed: Database Character Set is no longer showing current character set.
* Fixed: Missing font selection for body font in Atomik customizer when using Default skin.
* Fixed: Batch Task with empty batch does not finish running
* Fix Top Navigation Bar block 'include sticky nav' setting not set appropriately when editing the block
* Fixed inability to drag an individual block out of the stacks panel in a page.
* Fixed: Document Library advanced search fields do not display
* Fixed “Express form error dirty entity” error that users might see when creating forms on the front-end.
* Fixed bug where attribute data validation routines weren’t being run when updating certain objects and certain objects in bulk.
* Fixed: Express Calendar and Calendar Event Attributes Not Correctly Implemented
* Fixed: "Added to Page" File search filter doesn't work
* Fixed: Schedule Guest Access doesn't work (thanks HamedDarragi)
* Fixed: Page Search in chooser dialog doesn’t work (thanks HamedDarragi)
* Fixed: The multilingual panel/page relations panel didn’t allow you to create pages in the multilingual trees from the related page - and it used to.
* Fixed strange appearance in Dashboard sitemap selector when using multisite and multiple locales.
* Fixed bugs with using custom file attributes with the Document Library block.
* Fixed theme customizer not working on legacy LESS-based themes when being used with a large number of LESS variables.
* Fixed inability to see sort icons on attributes in the Dashboard.
* Fix Auto-Nav showing duplicate tabs in themes based on Bootstrap 3 (thanks lvanstrijland)
* Fixed: When using more than one user search criteria by group, one to include groups and one to exclude groups, we get the wrong results (thanks mnakalay)
* Fixed: Accordion block doesn't load required assets when not using BS5 based theme.
* Fixed Error when try to edit 'express details block' (thanks Ruud-Zuiderlicht)
* Fixed edit page type basic details error on PHP 8.
* Tooltips now work properly again in Composer interface (thanks danklassen)
* Fixed inability to create and update skins for themes that had a large number of parameters under certain conditions.
* Fixed errors that would occur when creating a site, enabling multilingual, setting a new source locale, and deleting the original default locale.
* Fixed: User activation workflow, Activate action not working
* Fixed: 9.0.2 Seo Bulk Updater for multilingual site not showing results when selecting All Levels (thanks danklassen)
* Fixed: Placing a Sticky "Top Navigation Bar" in Global "Navigation" using Atomik blocks editing of page
* Fixed: Topics Attribute Search Form is not getting translated on Frontend (thanks 1stthomas)
* Re-enabled the ability to edit a user’s avatar from their Dashboard details page.
* Fixed: Clipboard - Unable to remove broken clipboard entries/clipboard doesnt remove deleted blocks
* Fixed: When placing a stack, the edit mode menu is not displayed
* Fixed: Adding Options To Option List Page Attribute Undefined Array Key under PHP 8
* Fixed: Multilingual copy site tree with alias pages (thanks hissy)
* Fixed: v9 Elemental Block Edit Nav Tabs Broken (thanks ccmEnlil)
* Fixed: Error in updating package from marketplace incorrectly displaying itself under certain conditions (thanks JohnTheFish)
* Fixed: Accordion block editing interface rich text editor doesn’t have access to Concrete-specific features like file manager, sitemap, etc…
* Fixes ErrorException - Undefined property: Concrete\Core\Permission\Access\Entity\GroupCombinationEntity::$label under PHP 8 (thanks 1stthomas)
* Legacy form's "reply to this email address" checked state was not properly passed (thanks katzueno)
* Fixed errors with the legacy form (thanks mlocati)
* Fixed: Updating an express form handle can result in a table name that is too long for mysql
* Fix several user search fields not retaining their selected values (thanks mnakalay)
* Fixed: install with Elemental full fails due to undefined array key "titleFormat" under PHP 8
* Fix YouTube block responsive size class issue (thanks katalysis)
* Fixed Marketplace dashboard page broken under PHP 8
* Conversation rating stars now appear properly (thanks deek87)
* Fixed inability to remove an entry from the trash when that entry is an alias to an external link (thanks Ruud-Zuiderlicht)
* Fixed bug where core “Parallax Image” area custom template (deprecated) now works again
* Fix a bug with having multiple image blocks with on-hover attribute set on the page didn’t work reliably (thanks evgk)
* Fixed: Toolbar title styling interfering with intelligent search results in accessibility mode (thanks Mesuva)
* Fixed: Switch Language block default view does not work
* Fixed inability to use the “Express Entry Selector Multiple” form control type.
* [V9 RC]Fixed cookie not being cleared properly to open "add block panel" when using the sticky add panel and installing Concrete in a sub-directory
* Fixed: Position of the reCAPTCHA badge not shown correctly after saving
* Fixed errors in waiting for me when groups or users were deleted.
* Fix inability to set storage location from file details Dashboard page.
* Fixed bugs with thumbnails on alternate storage locations (thanks mnakalay)
* Fixed: concrete.debug.hide_keys' not working on Globals do to commented Code
* Fix IpAccessControlService check against specific access control category (thanks mlocati)
*  Access Control: fix sorting categories in the dashboard page (thanks mlocati)
* Fixed bug: When there's no time window, we currently ban IP addresses forever, even if we configure Concrete to only ban for X seconds. (thanks mlocati)
* Fixed bug: "Illegal mix of collations" when running reindex task when running under certain database conditions.
* Added “snippet.png” back into rich text editor so you can see that button.
* Fixed: Removing Author User From Page Attributes & Saving Throws Error 
* Fixed: Deleting Containers throws Access Denied error under certain in-page editing conditions.
* Fixed: Rich Text Page Attribute Composer "Source" Editing Hindered By Composer Autosave
* Fixed a bug in image processing (Imagine Library) that could lead to segmentation faults under certain conditions (thanks mlocati)
* Fixed: PlaceholderService error in thumbnail overview (thanks haeflimi)
* Fixed: Deleting Containers shows multiple delete modal windows under certain in-page editing conditions.
* Fixed: Top navigation block always loads the default site tree even in multilingual sites (thanks danklassen)
* Fixed inability to override session handler to database in config prior to installation and then install successfully.
* Fix missing none option in attribute display block (thanks JohnTheFish)
* Fixed: Stacks with no approved versions do not appear in stacks list

## Backward Compatibility Notes

* The `Concrete\Core\Express\Form\Validator\Routine\RoutineInterface` class and all classes that implement it has changed. The `validate` method now takes a nullable third parameter for the `Concrete\Core\Entity\Express\Entry` object that may or may not exist. This replaces the request type attribute. The request type can now be inferred - if the entry does not exist, we assume this to be an `ADD` operation. If the entry exists within the `validate` method, you are running an `UPDATE` operation.
* Block::duplicate() has changed its secondary parameter from $isCopiedWhenPropagated to $controllerMethodToTryAndRun. This lets us choose `duplicate_master` or the new `duplicate_clipboard` in certain situations. It is very unlikely that this should impact any custom code you have written as this is pretty deep in the Concrete internals.
* If you have customized the Document Library view template, please ensure that your `<form>` tag has a valid input button with the name `”search”`. This is checked in the controller in order to ensure searching is actually occurring. If you want to search by advanced file attributes, you’ll need this to be in place or else the Document Library controller will not check for attribute searching.

## Developer Updates

* Added `on_page_version_delete` event (thanks hathawayweb)
* Mail Importer code running on ancient Zend Mail code updated to PHP 7+ (thanks KevinBLT)
* Patches to third party libraries to allow for installation on PHP 8.1 w/Composer (thanks mlocati)
* htmlawed HTML sanitization library updated for better compatibility with HTML5.
* IP Access Control: add IpAccessControlCategory::describeTimeWindow() (thanks mlocati)
* Allow Date service class to work with DateTimeImmutable objects (thanks mlocati)
* Improvements and bug fixes to route building and controller syntax (thanks mlocati)
* More reliable running of on_start() in block controllers before page contents are rendered (thanks hissy)
* Moved concrete5/dependency-patches to the core composer.json instead of the separate composer project (thanks mlocati)
* Improved code commenting throughout all core blocks (thanks deek87)
* Fix list_syntax rule of PHP-CS-Fixer (thanks mlocati)
* Significant list of third party PHP script minor updates.
* Simplify c5:exec return code (thanks mlocati)
* Fixed: Task scheduling command is incorrect on dashboard page and in documentation, needs more detail
* `Concrete\Core\Http\ResponseFactory` used to take `$session` as its first constructor dependency, even though that was not used. This caused problems in the event response factory was used prior to sessions being available or being configured for database sessions that were not yet installed. This parameter has been removed. If you use the `$app->make()` method of building this class, you should not be affected.
* Now using https:// for communication with the Concrete marketplace even when the user’s site is not https://

## Security Fixes

* Fixed: https://hackerone.com/reports/1483104
* Fixed several places where we weren’t sanitizing file names in the file manager and stacks page.

# 9.0.2

## Behavioral Improvements

* Many translation fixes, including new components that weren’t localized (thanks mlocati)
* Better appearance of inline toolbars. Updates to remove potential style collisions between block design toolbar and themes.
* Improvements to the process of publishing page type default blocks to child pages (thanks deek87)
* Rehash passwords when needed to ensure adherence to the latest security standards.
* Fixed display of the FAQ block in edit mode.
* Use base64 encoding/decoding on submitting tracking codes in the Dashboard to avoid triggering mod_security (if present) on submit (thanks Mesuva)
* Added a settings tab with new options to Accordion block type (thanks katalysis)
* Concrete file choosers once again limit by file type and extension in certain contexts (e.g. no longer able to choose non-image files if the code requires image files be chosen.)
* Two Column Light and Light Stripe containers in Atomik theme renamed to Two Column Highlight and Highlight Stripe to avoid confusion.
* Stacked and Stacked Primary custom templates for Feature block in Atomik have nicer padding, better behavior when used to link elsewhere.
* Hero Image “Offset Title” custom template in Atomik now has better behaviors: it honors the height setting and looks nicer in the theme whether the container is enabled or not.
* Miscellaneous style classes added to the rich text editor when using Atomik theme.
* Improvements to the new “configurable thumbnails” responsive thumbnails in the Image block.
* Improvements to logo custom template and feature link CSS in Atomik theme.

## Bug Fixes

* Fixed fatal error when viewing Express object listings with associations in their list in a site updated from 8.5.x.
* Fixed Hero Image block button not linking anywhere
* Fixed Feature Link block button not linking anywhere
* Fixed error where block template view.css and view.js files were not loading properly.
* Fixed inability to start from a customized theme when using the legacy theme customizer.
* Fixed inability to delete files or clear sample data content when files were being used in a Board.
* Canonical URLs no longer include arbitrary query strings.
* Fixed inability to uninstall tasks when working with packages that had installed custom tasks.
* Fixed error when connecting to marketplace under PHP 8.
* Fix issue where sitemap is inaccessible to users on multilingual sites if the user doesn't have access to view the default locale in the sitemap.
* Fixed weird behavior when attempting to edit theme grid layouts in Atomik and other Bootstrap 5 themes.
* Fixed bug when deleting containers that had been aliased out from a master page removing the container on the master page as well.
* Fixed inability to sort entries in the Image Slider block.
* File trackability works much more reliably and across more core block types than before.
* Fixed: CollectionSearchIndexAttributes table is updated without approving the page version
* Fixed missing icons in Share this Page block (thanks hissy)
* Fixed: Layout toolbar partially off page window. Add Layout Function not working
* Fixed custom CSS not showing up in the customizer when editing a custom skin.
* Fixed fatal error when rendering /dashboard root page in PHP 8+.
* Fixed fatal error rendering Dashboard file detail screen in PHP 8+.
* Fixed fatal error when rendering gallery add block interface in PHP8+.
* Fixed bug where border radius wasn’t being saved properly in block/area design settings.
* Fixed error in Gallery block when images in it had been removed from the file manager.
* Fixed error “Trying to access array offset on value of type bool “ when logging in with a username that doesn’t exist under PHP 8 (should get an error that explains what you did wrong better than this).
* Many additional fixes for core block types in PHP 8 (thanks deek87)
* Fix “division by zero” error under some conditions when running queueable commands.
* Fixed bug where custom block cache override settings are reset on new version approval (thanks hissy)
* Fixed: If by any chance $buttonColor is unset, the class tag of the `<div>` is never closed (thanks puka-tchou)
* Theme responsive image breakpoints are now in the proper order to support the picture tags on mobile devices in Atomik.
* Color picker in image editor now displays properly (thanks mlocati)
* Fixed: Dashboard favorites menu aren’t localized properly (thanks mlocati)
* Fixed bugs with Hero Image block under PHP 8
* Fixed bugs with Feature Link block under PHP 8
* Fixed error in YouTube block view when using PHP 8.
* Fixed errors in Top Navigation Bar block under PHP 8
* Fixed error in Testimonial block when using PHP 8 (thanks hissy)
* Fix "Undefined array key" warning for advanced page search on php@8.0 (thanks hissy)
* Fix "variable is undefined" errors when adding Conversation blocks when using PHP 8 (thanks mlocati)
* Fixed Exception thrown when attempting to reload strings (thanks mlocati)
* Fixed inability to download files in the file manager via the “Download File” option in the file menu.
* Fixed broken Site attribute type.
* Fixed: When configuring a select attribute to allow a single selection but also allow end user additions, an error is received.
* Fixed: Adding a user unless multiple languages are installed fails under PHP 8
* Fixed: Board "Error Call to a member function getStylesheet() on null"  when rendering a Board in the Dashboard.
* Fixing issues viewing users in groups in Dashboard for sub-admins.
* Fixed: Exception uninstalling package/theme when package has installed containers
* Fixed: List of themes ready to install broken and has design issues (thanks mnakalay)
* Fix c5:entities:refresh CLI command (thanks mlocati)
* Fixed error when using files with UUIDs in the content block (thanks mnakalay)
* Fix position of caption in Language Details dialog (thanks mlocati)
* Fixed error adding Document Library block to the page.
* Fixed error “Unknown named parameter $html” when attempting to reset a password on PHP 8.
* Fixed: Document Library Block: Click on a folder leads to *Invalid folder ID*
* Fixed magnifying glass button in the search in the navigation bar is not working in the Top Navigation Bar block.
* Fixed some edge case errors with package uninstall and Doctrine entities
* Fixed error where database entities weren’t showing their directory locations on the Database Entities Dashboard page.
* Fixed error where uninstalling a package and reinstalling it doesn’t create the block type record in the package if there is only a single block type in the package and nothing else.
* Fixed errors installing Atomik documentation under PHP 8.
* Bug Fixes to Event List block in PHP 8.
* Fixed: Featured Event Toggle Not Working in Event List block.
* Fixed double select appearance on Edit File Thumbnail Dashboard screen.
* Fixed PHP 8 Error: Error on editing Page List block on brand new 9.0.1 install
* Fixed inability to set permissions against a particular user in advanced permissions mode (thanks hamzaouibacha)
* Dashboard Reports page now links over to legacy form results page when necessary (thanks mnakalay)
* Fix for broken area edit menu when advanced permissions were enabled under some conditions (thanks mnakalay)
* Fixed: Contrast off for edit button label when toolbar titles setting enabled
* Fixed image libraries check not running in Image Options single page (thanks mnakalay)
* Fixed: Elemental theme, Version 9.0.1: New Accordion Block not working properly

## Developer Updates

* Reverted Form helper behavior so that passing in `class` will append the CSS classes to whatever the default class was, rather than replace it fully. Added a new `classes` key that will fully replace the classes if present.
* Upgrade gettext/languages and punic/punic (thanks mlocati)
* Theme grid preset layouts now export properly and import properly when using the exporter/Content XML format (thanks mlocati)
* The canonical URL query string handler has been changed from excluded to included – meaning that if you as a developer want to include a query string parameter in your various canonical URLs, you’ll need to add the parameter key/name to the `site.siteName.seo.canonical_tag.included_querystring_parameters` parameter.
* CKEditor updated to 4.17.1 (thanks hissy)

# 9.0.1

## Behavioral Improvements

* Improvements to scheduled page version publishing (thanks hissy).
* Fixed login welcome back/desktop in Atomik theme (previously had JavaScript errors.)
* Performance improvements when retrieving access entities for users (thanks hissy)
* Updated translation library to 1.7.0 to allow 9.0 to be fully translated (thanks mlocati)

## Bug Fixes

* Fixed error when installing Elemental on PHP 8 (https://github.com/concrete5/concrete5/issues/10003)
* Many display issues fixed when browsing marketplace from within your 9.0 site.
* Fixed issue where updating from 8.5.6 would disable concrete extensions in rich text editor.
* Fixed Unknown column 'folderItemName' in 'field list’ in folder item list custom code used by add-ons.
* Fixed time dropdowns not working when editing a calendar event.
* Fixed inability to install 9.0 with Composer.
* Fixed some missing social icons for social link types.
* Fixed inability for legacy LESS themes to support rgb and rgba colors.
* Fixed broken Dashboard page: Excluded URL Word List
* Fixed inability to see proper options selected when editing user attribute key.
* Fixed ImageValue::setImageFileID() must be of the type int, string given when updating some legacy theme customizer values (thanks martinkouba)
* Fixed page summary templates link not working in page design panel.
* Fixed inability to open block custom design toolbar in PHP 8.
* Bug fixes to theme updates that use the text type customizer in certain situations (thanks martinkouba)
* Fixed: Non super admin cannot move a block pasted from clipboard (thanks jaromirdalecky)
* Bug fixes to legacy theme customizer with themes that used the same variable for different variable types.
* Fixed error Base table or view not found: 1146 Tablemessengerscheduledtasks' doesn't exist when upgrading from 8.5.x to 9.0.
* Fixed: Country select menu has the `form-control` class instead of `form-select`.

## Developer Updates

* Banned Words validation service classes completely refactored and modernized (thanks hissy)
* Make it so users can disable core middlewares (thanks mlocati)

## Security Fixes

* Fixed CVE-2021-22970: Concrete allowed local IP importing causing the system to be vulnerable to a. SSRF attacks on the private LAN servers and b. SSRF Mitigation Bypass through DNS Rebinding. Concrete now disabes all local IPs through the remote file uploading interface. Concrete CMS security team gave this a CVSS v3.1  score of 3.5 AV:N/AC:H/PR:L/UI:N/S:C/C:L/I:N/A:N This CVE is shared with HackerOne Reports #1364797 (Thanks Adrian Tiron from FORTBRIDGE (https://www.fortbridge.co.uk/ ) and #1360016 (Thanks Bipul Jaiswal) This fix is also in Concrete v 8.5.7

# 9.0.0

## Major New Features

* Boards
* Summary Templates
* Multisite support.
* New modern theme for 2021 – Atomik
* New Gallery block built into the core.
* Completely rebuilt file manager that has much better folder and advanced search support, support for home folders, favorite folders, external file providers, a new file upload UI and much much more.
* Completely new upload experience that adds support for additional service provider plugins.
* A completely new integrated image editor
* Overhauled theme customizer, with support for skins, non-customizable skins, SCSS support, Bootstrap 5 and more.
* Tasks: a completely rebuilt, much improved version of classic Concrete Jobs, with support for queueing, scheduling, unified input/output within the console and web interfaces, live output with Mercure and more.
* User Group Types: Add the ability to create types of groups, including roles within groups, group management based on roles within groups, and more.
* An overhauled UI built off of Bootstrap 5 and Concrete Bedrock

## Other New Features and Improvements

* Express now supports multisite.
* Added the ability to edit page aliases from within the Dashboard sitemap (thanks mlocati)
* Added the ability to customize the from name registration email parameter (thanks katzueno)
* New Breadcrumb Navigation block now available (thanks hissy)
* Much improved performance throughout, due to better navigation caching, and cache optimization (hissy and core team)
* Added pagination to clipboard panel and the ability to reset all clipboards from the Dashboard (thanks bitterdev)
* Added configuration for whether to log email body contents or just metadata (thanks bitterdev)
* Support for interactive theme documentation and block preview.
* Added bulk page permissions commands to the page search interface (thanks bitterdev)
* Added the ability to upload a CSV of users to assign to a particular group. (thanks bitterdev)
* Completely new image editor plugin framework. Ships with TUI Image Editor.
* New icon selector component when working with block types like Feature that allow users to select icons.
* Added logging for file uploads and file deletions (thanks bitterdev)
* File manager can now automatically populate file attributes from EXIF metadata on upload (thanks bitterdev)
* Implement Clear-Site-Data header after a successful login (thanks ahukkanen)
* Added block title format for Date Navigation block (thanks katalysis)
* Much improved Image block, including the ability to load images in lightboxes, display thumbnails of image in the page, and much more.
* add delete button to package that is just uninstalled or download (thanks hissy)
* Improved login performance when logging in with Remember Me cookie.
* New Page Version Comment field available in page composer (thanks hissy)
* Introduce new middlewares for security options (thanks hissy)
* User must now confirm the existing password when changing their own password or another user’s password in the Dashboard.
* Much improved asynchronous thumbnail generation process, with enhancements from the CLI task runner and Mercure (thanks bitterdev)

## Bug Fixes

* Files are not placed in a folder's selected storage location if it has a custom storage location (thanks danklassen)
* Fixes bug where files moved to folders were not using those folders storage locations (thanks danklassen)
* If a form redirects to an external page that includes a query parameter, the result is a malformed URL. (thanks JeffPaetkau)
* FIxed error when marking URL slug as required in composer form (thanks httnnnkrng)
* Fixed: User workflows - User activation does not trigger on admin email validations (thanks bitterdev)
* Document Library - Handle missing folder
* Avoid an exception on express_entry_detail block when the express form ID is not exists (thanks biplobice)
* Copied block with no edit mode has "edit block" link which throws excepetion (thanks gutig)
* Fixed bugs within Redis-powered full page caching driver (thanks matt9mg)

## Developer Updates

* Badges and community points have been removed from the core. If you need this functionality, install the Community Badges add-on from
  https://github.com/concrete5/community_badges prior to upgrading your site.
* Concrete now runs on PHP 8.
* Tools have been completely removed, including from blocks and packages. Their functionality has been more securely and flexibly available with the routing and controller systems for many years now. (thanks mlocati!)
* Completely rebuilt new queue system, built on Symfony Messenger.
* Completely new command/message system, built on Symfony Messenger.
* Many core components updated to their latest version, including Laravel and Symfony components.
* Add overridable collection handle generator (thanks hissy)
* Removing old process.php script for backend requests.
* Introducing a new command bus pattern. Developers can use to encapsulate their commands, reusing them with one or two lines in multiple places.
* Swapped underlying HTTP client with Guzzle and PSR7.
* Router adds support for single action controllers with __invoke (thanks shahroq)
* Allow Form helper to handle new HTML input types (thanks JohnTheFish)
* https://github.com/concrete5/concrete5/pull/9479 (thanks jeffPaetkau)
* Blacklist/whitelist terminology renamed throughout the core.

## Backward Compatibility Notes

* If you use `Core::make()`, `$app->make()` or anything similar in your packages, and provide arguments to these classes at the same time, recent updates to the Laravel Container class may break some older code. Please see this tutorial for more information: https://documentation.concretecms.org/tutorials/add-developers-get-your-add-ons-ready-concrete-cms-90
* Beginning in version 8, we added the ability to override core elements from within your themes. For example, if the core requires an element via `View::element(‘conversations/add_post’;` the core looks for this add-on in `concrete/elements/conversations/add_post.php`. However, if the currently active theme provides this element in `themes/my_theme/elements/concrete/conversations/add_post.php`, it will be used instead. We are changing this to remove the `concrete/` directory from the `elements` directory within your theme. That means in order to override any core element from within your theme, you only need to make it available at the same path within the `elements/` directory of your theme.
* If you register custom help for specific pages in your package, make sure to do so from within your package’s `on_start` method rather than from within the Dashboard page. Our new help panel requires this. See https://github.com/concrete5/concrete5/issues/9869#issuecomment-927136592 for more information.
* Console command `c5:blacklist:clear` has been renamed `c5:denylist:clear`
* If you work with Concrete cookies directly in your server configurations, be aware that they have been renamed. The default session cookie has been changed from CONCRETE5 to CONCRETE; the default is-logged-in cookie has been changed from CONCRETE5_LOGIN to CONCRETE_LOGIN.

# 8.5.9

## Bug Fixes

* Fixed inability to upload files when file chunking is disabled.
* Fixed bug that prevented file chunking from also working.
* Reverted code that accidentally made the core require PHP 5.6+ in some situations.

# 8.5.8

## Behavioral Improvements

* JavaScript and CSS assets now have the timestamp of when the cache was last cleared appended to them (thanks deek87, haeflimi)
* Renamed concrete5 to Concrete CMS and Concrete during the installation process.
* Nicer version history view in add-on update screen (thanks biplobice)

## Bug Fixes

* Fixed error that would occur if you deleted an Express entry and then attempted to reorder that same entry on the page before reloading (thanks biplobice)
* Fixed error where users, files and sites weren’t being reindexed when running the `index_search_all` job.
* Fixed error where copying conversation blocks out from page defaults made them all one instance of the same conversation (thanks hissy)
* Validating Express, User and Page attribute types now works when used with Composer and Expres (thanks hissy)
* Fixed bug in Redis caching backend when saving a primitive value.
* Fixed: when using the Express Form block, and a file is uploaded through the form, it creates two versions of the file, which are seemingly identical (thanks 1stthomas)
* Fixed: Clear old page versions in all site trees when running remove page versions job (thanks Ruud-Zuiderlicht)
* Fixed bug where OAuth2 and sign in as user functionality could lead to someone unintentionally joining their user account to a different account.
* Render single pages like 404, 403, login, register in default site locale (thanks hissy)
* Fixed: : error message doesn't display when upload file failed via drag & drop (thanks hissy)
* Fixed invalid and unhelpful displaying on marketplace connection failures during certain conditions (thanks JohnTheFish)
* Topics Attribute Search Form is not getting translated on Frontend (thanks 1stthomas)
* Fixed: Multilingual copy site tree with alias pages (thanks hissy)
* Fix migration bug on fix overlapping start end dates when custom page publishing dates had been set in some cases (thanks hissy)
* Fixed null pointer Exceptions when using area layouts under certain conditions (thanks biplobice)

## Security Fixes 

* CKEditor updated from 4.16.2 to 4.18.0 (thanks hissy)
* Remediated CVE-2022-21829 - Concrete CMS Version 9.0.2 and below and 8.5.7 and below can download zip files over HTTP and execute code from those zip files which could lead to an RCE. Fixed by enforcing ‘concrete_secure’ instead of ‘concrete’. Concrete now only makes requests over https even if a request comes in via http. Concrete CMS security team ranked this 8 with CVSS v3.1 vector: AV:N/AC:H/PR:H/UI:N/S:C/C:H/I:H/A:H Credit goes to Anna for reporting on HackerOne - https://hackerone.com/reports/1482520
* Remediated CVE-2022-30117 -  Concrete CMS version 9.0.2 and below and 8.5.7 and below allowed traversal in  /index.php/ccm/system/file/upload which could result in an Arbitrary File Delete exploit. This was remediated by sanitizing  /index.php/ccm/system/file/upload to ensure Concrete doesn’t allow traversal and by changing isFullChunkFilePresent to have an early false return when input doesn't match expectations.Concrete CMS Security team ranked this 5.8 with CVSS v3.1 vector AV:N/AC:H/PR:H/UI:N/S:C/C:N/I:N/A:H. Credit to Siebene for reporting https://hackerone.com/reports/1482280
* Remediated CVE-2022-30120 - XSS in /dashboard/blocks/stacks/view_details/ -  old browsers only.  When using an older browser with built-in XSS protection disabled, insufficient sanitation where built urls are output can be exploited for Concrete CMS version 9.02 and below and Concrete CMS 8.5.7 to allow XSS. This cannot be exploited in modern-day web browsers due to an automatic input escape mechanism. Dashboard Stacks page sort URLs are now sanitized. Concrete CMS Security team ranked this vulnerability 3.1 with CVSS v3.1 Vector AV:N/AC:H/PR:N/UI:R/S:U/C:N/I:L/A:N. Sanitation has been added where built urls are output. Credit to Bogdan Tiron from FORTBRIDGE (https://www.fortbridge.co.uk/ ) for reporting https://hackerone.com/reports/1363598
* Remediated CVE-2022-30119 - XSS in /dashboard/reports/logs/view -  old browsers only.  When using Internet Explorer with the XSS protection disabled, insufficient sanitation where built urls are output can be exploited for Concrete CMS version 9.02 and below and Concrete CMS 8.5.7 to allow XSS. This cannot be exploited in modern-day web browsers due to an automatic input escape mechanism. Concrete CMS Security team ranked this vulnerability 2 with CVSS v3.1 Vector AV:N/AC:H/PR:H/UI:R/S:U/C:N/I:L/A:N. Sanitation has been added where built urls are output. Thanks zeroinside for reporting https://hackerone.com/reports/1370054
* Remediated CVE-2022-30118 - XSS in /dashboard/system/express/entities/forms/save_control/[GUID]: \  old browsers only.
When using Internet Explorer with the XSS protection disabled, editing a form control in an express entities form for Concrete CMS version 9.02 and below and Concrete CMS 8.5.7 and below can allow XSS. This cannot be exploited in modern-day web browsers due to an automatic input escape mechanism. Concrete CMS Security team ranked this vulnerability 2 with CVSS v3.1 Vector AV:N/AC:H/PR:H/UI:R/S:U/C:N/I:L/A:N. Thanks zeroinside for reporting https://hackerone.com/reports/1370054

# 8.5.7

## Bug Fixes

* Fixed issue where remote updater would read the entire update into memory, leading to potential out of memory errors when updating the core.
* Fixed error when setting global calendar permissions in the Dashboard.
* Fixed issue where reset users weren’t properly notified when logging in that their passwords needed to be changed (thanks hissy)
* Fixed: reCAPTCHA timout after 2min (thanks JeffPaetkau)
* Fixed: fatal error on upgrade french version 8.5.5 to 8.5.6, "2 plural forms instead of 3" (thanks mlocati)
* Fixed error with rich text conversation editor not working (Thanks hissy)
* Fixed issue with URLs being case sensitive in some internationalization cases (thanks dimger)
* Fixes to topic attribute search index content (thanks hissy)
* Maintenance mode now returns the 503 HTTP error code when running (thanks hissy)
* Fix Call to a member function isDefault() on null" error on the site upgraded from 5.7 when using the migration tool (thanks hissy)
* Fixed issue where rich text attribute type wasn’t showing a full toolbar (note: in the future we want to make this an option, and strongly recommend users use this smaller, sanitized toolbar – but it should be an option, not the default.)
* If a file has a password in the file manager, you will not be able to view it inline in the rich text editor.
* Fixed: Changing database charset in dashboard throws error: call to a member function add() on null (thanks myq)

## Library Updates

* Bump CKEditor from 4.16.1 to 4.16.2 (thanks hissy)

## Security Fixes

* Fixed  CVE-2021-22966 - Privilege escalation from Editor to Admin using Groups in Concrete CMS versions 8.5.6 and below.  If a group is granted "view" permissions on the bulkupdate page, then users in that group can escalate to being an administrator with a specially crafted curl.  Fixed by adding a bulk update permission security check. Concrete CMS Security team CVSS scoring: 7.1 AV:N/AC:H/PR:L/UI:R/S:U/C:H/I:H/A:H  Credit for discovery: "Adrian Tiron from FORTBRIDGE ( https://www.fortbridge.co.uk/ )" This fix is also in Concrete version 9.0.0
* Fixed CVE-2021-40101: Admin users must now provide their password when changing another user’s password from the Dashboard.Concrete CMS security team CVSS scoring is 6.4 AV:N/AC:H/PR:H/UI:R/S:U/C:H/I:H/A:H. Credit for discovery: "S1lky”. This fix is also in Concrete version 9.0.0
* Fixed CVE-2021-22968: A bypass of adding remote files in Concrete CMS File manager lead to remote code execution. We added a check for the allowed file extensions before downloading files to a tmp directory. Concrete CMS Security Team gave this a CVSS v3.1 score of 5.4 AV:N/AC:H/PR:H/UI:R/S:C/C:N/I:H/A:N Thanks Joe for reporting! This fix is also in Concrete version 9.0.0
* Fixed CVE-2021-22951: “Unauthorized individuals could view password protected files using view_inline”. Concrete CMS now checks to see if a file has a password in view_inline and if it does we don’t render the file. Concrete CMS security team CVSS scoring is 5.3  AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N  Credit for discovery: "Solar Security Research Team". This fix is also in Concrete version 9.0.0
* Follow up fix for CVE-2021-40107: Stored XSS in comment section/FileManger via "view_inline" option.  We were informed the fix put into version 8.5.6 was not sufficient. Thanks "Solar Security Research Team". We now check to see if a file has a password in view_inline and, if it does, we don’t render the file. Concrete CMS security team CVSS scoring is 5.3:  AV:N/AC:L/PR:N/UI:N/S:U/C:L/I:N/A:N This fix is also in Concrete version 9.0.0
* Fixed CVE-2021-22967: insecure indirect object reference (IDOR); an unauthenticated user was able to access restricted files by attaching them to a message in a conversation. To remediate this, we added a check to see if a user has permissions to view files before attaching the files to a message in "add / edit message”. The Concrete CMS security team gave this a CVSS v3.1 score of 4.3 AV:N/AC:L/PR:L/UI:N/S:U/C:L/I:N/A:N  Thanks Adrian H for reporting! This fix is also in Concrete version 9.0.0
* Fixed CVE-2021-22969 : SSRF mitigation bypass using DNS Rebind attack giving an attacker the ability to fetch cloud IAAS (ex AWS) IAM keys. To fix this, Concrete CMS no longer allows downloads from the local network and specifies the validated IP when downloading  rather than relying on DNS. The Concrete CMS team gave this a CVSS v3.1 score of 3.5 AV:N/AC:H/PR:L/UI:N/S:C/C:L/I:N/A:N . Discoverer: Adrian Tiron from FORTBRIDGE (https://www.fortbridge.co.uk/ ) Please note that Cloud IAAS provider mis-configurations are not Concrete CMS vulnerabilities. A mitigation for this vulnerability is to make sure that the IMDS configurations are according to a cloud provider's best practices. This fix is also in Concrete version 9.0.0
* Fixed CVE-2021-22970: Concrete allowed local IP importing causing the system to be vulnerable to a. SSRF attacks on the private LAN servers and b. SSRF Mitigation Bypass through DNS Rebinding. Concrete now disabes all local IPs through the remote file uploading interface. Concrete CMS security team gave this a CVSS v3.1  score of 3.5 AV:N/AC:H/PR:L/UI:N/S:C/C:L/I:N/A:N This CVE is shared with HackerOne Reports #1364797 (Thanks Adrian Tiron from FORTBRIDGE (https://www.fortbridge.co.uk/ ) and #1360016 (Thanks Bipul Jaiswal) This fix is also in Concrete v 9.0.1

# 8.5.6

## New Features

* Added Session Options Dashboard page that will allow administrators to configure many aspects of the session cookie.

## Behavioral Improvements

* Added support for translation placeholders (thanks shahroq)
* Re-enabled connect to community for the marketplace; reworked to sidestep issues with browser cookie compatibility
* Add autocomplete=off to various password fields.
* "Index Search Engine - Updates" job should not re-index all entries (thanks hissy)
* Fix default formatting of datetime exports in express export csv (thanks deek87)
* Improvements to IP parsing for actions like allowlist/blocklist (thanks mlocati)

## Bug Fixes

* Fixed error when pages weren’t getting accurately set in the full page cache.
* Fixes for errors/warning occurring with PHP 7.3 and 7.4 when "Consider warnings as errors" is set (thanks arielkamoyedji)
* Additional dialogs within CKEditor link dialog (Sitemap, Browse Server) prevent further page scrolling even after being closed (thanks hissy)
* Fix error attaching a Facebook account to a user profile (thanks biplobice)
* Fixed disappearing survey and calendar event dialogs in some cases (thanks hissy)
* Bug fixes on switching language using the Switch Language block (thanks biplobice)
* Fixed inability to save channel logging settings on the Dashboard page (thanks Hmone23)
* Fixed bug where layouts can’t be moved above blocks (thanks Haeflimi)
* Fixed bug in the 8.5 file manager when selecting on single file in multi-file selector (thanks deek87)
* Fix to show page drafts created by the current user (thanks hissy)
* Fix user selector attribute being un-searchable (Note: you will have to recreate your attributes before they are properly searchable).
* Bug fixes to search popup with pagination (thanks deek87, katz, hissy)

* Fixed 403 Error in Page Defaults when using REDIS for Caching (thanks deek87)

## Security Fixes

(Special thanks to Solar Security Research Team and Concrete CMS Japan)

* Fixed Hackerone report 1102067, CVE-2021-40097: Authenticated path traversal to RCE by adding a regular expression
* Fixed Hackerone report 1102080, CVE-2021-40098: Path Traversal leading to RCE via external form by adding a regular expression
* Fixed Hackerone report 982130, CVE-2021-40099: RCE Vulnerability by making fetching the update json scheme from concrete5 to be over HTTPS (instead of HTTP)
* Fixed Hackerone report 616770, CVE-2021-40100: Stored XSS in Conversations (both client and admin) when Active Conversation Editor is set to "Rich Text"
* Fixed Hackerone report 921288, CVE-2021-40102: Arbitrary File delete via PHAR deserialization
* Fixed Hackerone report 1063039, CVE-2021-36766: Security issues when allowing phar:// within the directory input field. (thanks deek87)
* Fixed Hackerone report 1102211, CVE-2021-40103: Path Traversal to Arbitrary File Reading and SSRF
* Fixed Hackerone report 1102088, CVE-2021-40104: SVG sanitizer bypass by swapping out the  SVG sanitizer in the core with this third party library darylldoyle/svg-sanitizer
* Fixed Hackerone report 1102054, CVE-2021-40105: Fixed XSS vulnerability in the Markdown Editor class in the conversation options
* Fixed Hackerone report 1102042, CVE-2021-40106: Unauth stored xss in blog comments (website field)
* Fixed Hackerone report 1102020, CVE-2021-40107: Stored XSS in comment section/FileManger via "view_inline" option
* Fixed Hackerone report 1102018, CVE-2021-40108:  Adjusted core so that ccm_token is verified on  "/index.php/ccm/calendar/dialogs/event/add/save" endpoint
* Fixed Hackerone report 1102225 which was split into two CVEs: An attacker could duplicate topics and files which could possibly lead to UI inconvenience, and exhaustion of disk space.
* For CVE-2021-22949: Added checking CSRF token when duplicating files in the File Manager.
* For CVE-2021-22953: Added checking CSRF token when cloning topics in the sitemap.
* Fixed Hackerone report 1102177, CVE-2021-22950: To fix CSRF in conversation attachment delete action, updated core to verify ccm_token when conversation attachments are deleted.
* Fixed Hackerone report 1102105, CVE-2021-40109:  To fix a reported SSRF vulnerability, the core was updated to disable redirects on upload, add an http client method to send request without following redirects, and put in a number of url/IP protections (examples: blocked big Endian urls, blocked IP variants from importing, prevented importing from hexadecimal/octal/long IPs)

# 8.5.5

## New Features

* Let user specify the SMTP HELO/EHLO domain for their SMTP server (thanks mlocati)

## Behavioral Improvements

* Removed version from meta generator tag.
* CKEditor updated to 4.15.0 (thanks mlocati)
* Page drafts are now viewable by the view page draft permission (thanks HMone23)
* Updated list of UK counties (thanks Mesuva)
* Update CKEditor from 4.15.0 to 4.15.1 (thanks mlocati)
* Fix: make email log readable by decode quoted printable text (thanks hissy)

## Bug Fixes

* Fixing bug where accidentally re-saving a theme preset layout (e.g. “Left Sidebar”) as a user preset would cause a site to become unresponsive.
* Fixed bug where pages indexed through the CLI search index job weren’t indexed properly (thanks haeflimi)
* Page Selector attribute now properly searchable (thanks dimger)
* No longer fire event `execute_job` twice (thanks deek87)
* Fixing error when rescanning a multilingual locale (thanks mlocati)
* Fixed error or max execution timeout that can occur when logging out of multilingual websites (thanks hissy)
* Fixed: [CKEDITOR] Error code: editor-element-conflict. (thanks mlocati)
* Fixed error: No such file or directory error when editing an aliased block which is not editable (thanks mlocati)
* Fix some issues when using tags on multilingual site (thanks hissy)
* Fix duration of IP bans (they were supposed to last seconds but instead used the same value and in minutes) (thanks mlocati)
* Fixed: Stacks don't update if caching is enabled (thanks hissy)
* Correctly parse non-decimal IP addresses (thanks mlocati)
* Fix: enable to send private message to all groups at once (thanks hissy)
* Fixed: Redis cookie handler always use the session name as a prefix (thanks mlocati)
* Fixed an error where 404 does not work in multi language cases under certain situations (thanks hissy)
* More resilient upgrade routine when dealing with conflicting character sets in mysql (thanks mlocati)
* Fix issue where a rich text field on a form block doesn't re-populate contents after submit (thanks Mesuva)
* Fixed: Express Forms - CSV Export does not respect datetime format from config (thanks 1stthomas)
* Fix bug: Express Form can generate same attribute keys for multiple attribute keys (thanks hissy)
* Fixes filtering by multiple topic attributes on an item list (thanks hissy)
* Banned words with multibyte characters are now accurately detected (thanks hissy)
* Use UserMessageException when invalid path traversal is detected (thanks mlocati)
* Do not remove picture elements on rendering textarea attribute value (thanks hissy)
* Fix "call to a member function overrideCollectionPermissions() on a non-object" in AreaAssignment (thanks mlocati)


## Security Fixes

* Fixed CVE-2021-28145 XSS in Surveys fixed (thanks deek87)
* Fixed CVE-2021-3111 Stored XSS on express entries H1 report 873474


## Developer Updates

* Allow routes with optional arguments (thanks mlocati)

# 8.5.4

## Bug Fixes

* Fixing update errors that can happen (Update causes exception): https://github.com/concrete5/concrete5/issues/8729 (thanks mlocati)
* 8.5.3 incorrectly enabled multisite extensions that aren’t ready until version 9. These are disabled in 8.5.4.
* Fix certain occasions where editing pages would result in composer being unable to load blocks. Fixes error “Unable to load block into composer” (Note: this will fix the issue for pages going forward, but existing pages with this error will not be resolved.)

## Additional Functionality Present in 8.5.3 not described in previous release notes

### New Features (Note: some of these are present in 8.5.3)

* Added the ability to copy, paste, import and export style customizer settings at the page level (thanks mlocati)
* Added new public identifier property to express entries; you can use this identifier to relate entries to each other, or within custom API requests in such a way that it can’t be guessed.
* Added a new Group custom attribute type for use with Express.
* Added the ability to specify file storage locations at the file folder level (thanks marvinde)
* Added the ability to send private messages to all users in a specific group.
* CSV files exported from Express objects now containing association data.
* Added the ability to show/hide survey results in the survey block.
* Added a console command to export express entities.
* Added the ability to require associations be selected in Express forms.
* Running the reindex search all function will now reindex all Express entities and entries as well.

### Behavioral Improvements (Note: some of these are present in 8.5.3)

* Improvements to code quality, speed and efficiency (thanks mlocati)
* Improvements to file importer code quality, better sanitization of problematic SVGs on upload. (thanks mlocati)
* Much improved address attribute logic and presentation for non North American countries/provinces/states – see https://github.com/concrete5/concrete5/issues/7943 (thanks ahukkanen)
* We now refresh the file manager after changing properties (thanks marvinde)


### Developer Improvements (Note: some of these are present in 8.5.3)

* Added coding style guideline sniffer using phpcs directly into the concrete5 console (thanks mlocati)
* Refactored file importer, added support for pre and post processors (thanks mlocati)
* Generalizes IP Blocking, making it easier for developers to add support for blocking IPs based on custom actions (thanks mlocati)
* Cleanup and improvements to the c5:package:pack command (thanks mlocati)

# 8.5.3

## New Features

* Added the ability to display the version status on the results page of a Page Search (thanks biplobice)
* Added the ability to log API requests via a Dashboard setting (thanks Kaapiii)
* Add phone and email to social links (thanks mlocati)
* The YouTube Video block now supports lazy loading. (Thanks MrKarlDilkington)

## Behavioral Improvements

* Moves the custom block template selector from the advanced tab to buttons (thanks Mesuva)
* YouTube block: Delete 'show video infomation' option and change option name of showing related videos (thanks yuuminakazawa)
* Return a response object instead of exiting after saving a block (thanks mlocati)
* Fixed: We don't have to generate thumbnails if the image is in the private storage location (thanks hissy)
* Fixed potential errors that could result when adding invalid regular expressions into the Google authentication type whitelist/blacklist (thanks mlocati)
* When you uncheck “include attribute in search index” then the columns will be fully removed from the search indexing tables (thanks mlocati)
* Update OAuth password check to use PasswordHasher class (thanks Mesuva)
* CKEditor: turn off 'Edit Source' before submit (thanks mlocati)
* Fix issue with sitemap generation in multilingual sites (thanks dimger)
* concrete5 handle the session garbage collection if a server isn’t going to do it (thanks mlocati)
* Select Multiple now works from within the file manager again (thanks deek87)
* When the user opens "Schedule Publishing" dialog, show a warning message if there is another scheduled version (thanks hissy)
* Add "Cancel Scheduled Publish" button in "Publish Pending" dialog (thanks hissy)
* Show a logout view to logged in users on the login page
* More logging during OAuth attach/detach attempts.
* Added a unique page ID class to each page for page targeting (thanks Shahroq)
* Added a blacklist of file extensions to ensure that developers can’t easily add PHP to a list of uploadable file types (thanks mlocati)
* Improves to logout speed under certain circumstances (thanks kkyusuke)
* Calendar block height set to auto for better display in small width areas (thanks nakazanaka)
* Fixed: getUserAccessEntityObjects returns guest if no session found (thanks biplobice)
* The Refresh Token grant is now available for OAuth2 APIs (thanks kkyusuke)
* Use local date time format in CSV (thanks hissy)
* Faster and safer duplication of FAQ/Image Slider blocks (thanks mlocati)
* Added an exception in case there's no template file to render (thanks iampedropiedade)
* Added raw and samesite options to cookie (thanks iampedropiedade)
* Improve distinction between log severity icons (thanks JohnTheFish)

## Bug Fixes

* Fixed inability to save blocks or do much of anything on Chrome 83 (relates to Chrome 83 behavioral change) (thanks bikerdave)
* Fixing not sending password to RedisArray in session and cache drivers (thanks deek87)
* Fixed bug where unnecessary localized stacks are generated when adding stacks to a multilingual site (thanks hissy)
* Fixed: 8.5.2 - Chunked file uploads generate multiple files in the backend (thanks ahukkanen)
* Fix flat sitemap in the trash view (thanks hamzaouibacha)
* Fixed: Given a calendar event that was starting yesterday and ends tomorrow. It's a strange behavior if this event doesn't show up today in the calendars "events list" block (thanks core77)
* Fixed multiple issues with user groups (thanks deek87)
* Failed to upload avatar on user account page because of ccm\_token error (thanks deek87)
* Fix file manager issue with number of items per page (thanks biplobice)
* Fixed: Thumbnails broken for storage locations outside web root (thanks hissy)
* Fixed: Unable to detach google account at My Account page due to null exception (thanks deek87)
* Fixed inability to move multiple pages at once in certain situations (thanks wordish)
* Unable to paste the screenshot into content block (thanks deek87)
* Fixed: Failing block validation denies any further access to that block if you cancel editing (thanks jlucki)
* Fix user-selector events firing more than once (thanks deek87)
* Fixed: CSS of Free-Form Layouts (or 'Custom Layouts') isn't loaded if the visitor is not logged in (thanks Ruud-Zuiderlicht)
* Fixed inability to insert a link in Rich Text editor custom attributes in the Dashboard context (thanks mlocati)
* Fixed XSS issue where admin could insert tags into image slider titles.
* Fix error caused by invalid sort direction.
* Build youtube embed url with the league url class to fix issues when malicious admin uses invalid URLs.
* Fixed: [Bug] Single pages lose their path if location is resaved in sitemap or composer.  (thanks dimger)
* [Fix] Image block hover option doesn't work for responsive images using the picture tag (thanks biplobice)
* Fixed error when the sortBy column isn't exists on the advanced search result (thanks biplobice)
* Fixed: Setup on Child Pages updates all pages of the type, not the type / template combination (thanks danklassen)
* Fixed: getUserAccessEntityObjects returns guest if no session found (thanks deek87)
* Fixed: The folder name is null when you create it with name '0' (thanks biplobice)
* Fix setting the emails subject a second time with an undefined variable (thanks Kaapiii)
* Fixed: 404 does not work in multi language case (thanks Kaapiii)
* Fixed: CKEDITOR errors shown in console (thanks mlocati)
* BC Fix: Make it so routes can echo their output (thanks mlocati)
* Fix token error on flag\_conversation\_message (thanks guyasyou)
* Fix document library block error when file node type is other than File or FileFolder (thanks biplobice)
* Fixed: Unable to save layout if it contains a Form block (thanks mlocati)
* Fix Fix initializing country/province link (thanks mlocati)
* Avoid exception on express attribute form during certain edge cases (thanks biplobice)
* HackerOne security fixes (thanks mlocati)
* Fix error on submitting workflow request to a deleted user (thanks hissy)
* Fix height/width of edit folder permissions dialog (thanks deek87)
* php 7.2 fix for updating a conversation message (thanks danklassen)
* Replying to a conversation does not clear editor (thanks danklassen)
* Don't check POSIX permissions of API public key on Windows (thanks mlocati)
* Fixing draggable zone on filemanager to only accept file/folder nodes (thanks deek87)
* Fixed: Currently in version 8.5.x sites that have been upgraded from 5.7 sites, you can no longer replace files (thanks deek87)
* Fixed upgrading from 5.7 under certain database circumstances (thanks mlocati)
* Fix wrong translatable strings placeholders (thanks mlocati)
* Fixed: Loading malformed html into a content block does some funky stuff (thanks mlocati)
* Fix H1 report 753567 (thanks hissy)
* Aliases are now shown in the Dashboard menu (thanks Ruud-Zicherlicht)
* make `c5:package:uninstall --trash` not throw exception if there wasn't a problem (thanks nklatt)
* Fix: Creating folders in the file manager doesn't create them in the right place
* Fixed: Deleting a Form block instance for an Existing Express Entity Form can delete the original entity (thanks dimger)
* Avoid error on save page list block options with empty custom topic node (thanks hissy)
* FIxed bug in alphabetizing multilingual sections (thanks biplobice)
* Fixed bug where public date/time page property wasn’t being properly validated if it was marked as required in a composer form (thanks matt9mg)
* Fixed potential YouTube block exception (thanks matt9mg)
* Fixed: select filterByAttribute can return all results (thanks matt9mg)
* Fixed order of parameters in some `implode()` methods (thanks shahroq)
* Fixed PHP errors raised when calling View::action() method of an attribute (thanks mlocati)
* Fixed certain block type errors in advanced permissions and stacks (thanks mlocati)
* Fixed: CLI update fails if there is a package dependency such as MultiStep Workflow add-on

## Developer Improvements

* Allow nested containers in custom theme layout presets (thanks jneijt)
* Allow the AuthorFormatter class to be overridden (thanks danklassen)
* Update concrete5 Translation Library (thanks mlocati)
* Code cleanup and improvements (thanks mlocati)
* [Fix] Config command with env option (thanks biplobice)
* Correctly set express entity package reference during import (thanks olsgreen)
* Added new `buildRedirect` method for easily creating redirects that honor the framework middleware from within controller methods (thanks mlocati)
* We now test installation and upgrades within Docker in our unit test suite (thanks mlocati)
* Update punic to 3.5.1 (thanks mlocati)
* Add the ability to easily inject custom Config drivers (loaders/saves) and implement Redis drivers.
* Fix phpdoc of the \Concrete\Core\Form\Service\Validation::test() (thanks biplobice)
* Fixed bug where update process wouldn’t use the interface LongRunningMigrationInterface to increase timeout (thanks mlocati)
* Add ForeignKeyFixer and c5:database:foreignkey:fix CLI command (thanks mlocati)

# 8.5.2

## New Features

* You can now control the number of results in the file manager from the file manager directly without loading the advanced search dialog (thanks marvinde)
* You can now delete all entries from an existing Express object without deleting the object.
* Update CKEditor from 4.11.1 to 4.12, add Placeholder plugin (thanks mlocati)
* Add the ability for each Express Form block to have its own from address (thanks dimger)
* Added the ability to set a background color for thumbnails and for use with the image editor (thanks marvinde) 
* Added the ability to search attributes when adding attributes to the page composer form (thanks iampedropiedade)
* The Page Attribute block can now use custom templates (thanks danklassen)
* Add GUI to configure trusted headers received by a proxy (thanks mlocati)
* Add dashboard page to change database character set / collation (thanks mlocati)
* ReCaptcha is now included as a captcha option in the core (thanks edbeeny and mlocati)
* You can now include page aliases in searches in the Dashboard advanced page search (thanks HamedDarragi)
* Allow email sending enable/disable from the dashboard (thanks biplobice)
* Make it configurable whether or not to ignore page permissions for RSS feeds (thanks hissy)
* Added the ability to show captions by default for the YouTube block (thanks ahukkanen)
* Added a new install theme console command (thanks AdamBassett)

## Behavioral Improvements

* Add MySQL version and SQL\_MODE to environment information (thanks mlocati)
* Removed the extraneous exception stack trace when the MySQL connection fails during installation (thanks mlocati)
* Added support for right-to-left languages in the concrete5 translate UI (thanks mlocati)
* Fix error where sitemap panel would show up even if the user has no access to add pages or to the sitemap.
* Improved uniformity between search interfaces in the Dashboard and dialogs for things like files, pages. Miscellaneous display bug fixes for search interfaces.
* Add the author column on express entries CSV export (thanks biplobice)
* Added file read route to the rest api (thanks deek87)
* Use the HTTP 303 code for downloading files instead of HTTP 302 (thanks dimger)
* Simplify the error message when copying a file to folder (thanks mlocati)
* Added Choose New File to the top of the file selector menu to help users confused by the “Replace” option further below (thanks mlocati)
* If the form redirects to a thank you page, pass the entry id so that the page can interact with the entry if desired. (thanks JeffPaetkau)
* We now separate titles and content of installation errors if you encounter them (thanks mlocati).
* In the desktop draft block, deleting a draft now no longer redirects you to the home page (thanks hamzaouibacha)
* Improved reliability when uploading large files into the file manager (thanks mlocati)
* RSS feed URL slugs can now have hyphens in them (to match the behavior of other concrete5 URL slugs) (thanks bikerdave)
* Added rel=noopener noreferer to different places in the core where we link to external pages, enabling better process management (thanks dimger)
* Added Twitch Social Link (thanks core77)
* Composer and block editing will no longer log you out while you are editing for a long period of time (thanks mlocati)
* Remember me 2 weeks value is now configurable (thanks iampedropiedade)
* Routing system now handles response objects returned by any controller `on_start()` methods (thanks mlocati)
* Add a config key to support script-specific locales (thanks hissy)
* Added the ability to disable checking for core and package updates when using concrete5 via composer (thanks mlocati)
* Improvements to the display of the feature block icon selector (thanks shahroq)
* PageTypeDefaults::SetupOnChildPages: Make Update forked blocks optional (thanks HamedDarragi)
* Reduced the number of errors Doctrine complains about when inspecting the mapipng information for the core entity classes (thanks macserv)
* Spelling errors fixed in certain error messages (thanks edbeeny)
* Set quoted-printable encoding for outgoing emails for better compatibility (thanks mlocati)
* Improvements to how the My Account menu was displayed in certain themes (thanks mlocati)
* Don't ask to preserve old page path of external URLs (thanks mlocati)
* When creating external links, the URL slug we generate is now based off the name of the link instead of the link (thanks dimger)
* Better localization in edit mode of calendar, by including localized version of moment.js (thanks mlocati)
* Brought back the ability to drag a file immediately into the file manager and have it begin uploading (Thanks mlocati)
* Add asset version number to cache bursting query string (thanks mnakalay)
* Show only the message when we have in case of UserMessageException (thanks mlocati)
* Fixed - SEO issue: <meta rel="canonical"> tag ignores any actions of page/block controller (thanks hissy)
* Attribute controllers can now define the “No Value” text (thanks mlocati)
* Reduced size of bundled bootstrap libraries; removed missing references to glyphicon font file

## Bug Fixes

* Fixed bug where XSS could be passed through to the select form helper under certain conditions.
* Fixed bug when using the document library when MySQL has ONLY\_FULL\_GROUP\_BY enabled (thanks dimger)
* Fixed bug where additional cancel and submit search buttons were showing up in advanced search dialogs.
* "Order Entries" page is not installed on upgrading from version 7 (thanks hissy)
* Fixed buggy behavior when searching by associations in Express.
* Fixed: Search Presets in dialog not actually submitting (thanks deek87)
* Fixed: Bugs with search presets not being deletable, searching JS errors when working with search presets (thanks deek87)
* Fixed bug with autoplay not starting in YouTube block due to https://developers.google.com/web/updates/2017/09/autoplay-policy-changes (thanks edbeeny)
* Fixed bug when Express form sends notification with an image/file attribute and it’s not filled out (thanks a3020)
* Add new Italian Province: South Sardinia (thanks mlocati)
* Fix error where adding an image or a file to composer would complain about it not being present, even if it was.
* Fixed error where file usage dialog did not work with files linked in the content block (thanks jeverd01)
* Fixed bug where navigating directly to dispatcher.php would throw PHP errors.
* Fixed error where global password reset didn’t require typing the confirm code. 
* FIxed inability to unapprove a page version in the versions menu (thanks kzn-a)
* Fixed: Password Requirements dashboard page was not installed via 8.5.0 & 8.5.1 fresh install (thanks katzueno and hissy)
* Fixed bug where clicking publish on a composer page draft could still create an extra version in some cases (thanks ahukkanen)
* Fixed: ccmAuthUserHash cookie and "Stay signed in" functionality allows user impersonation if hash table is leaked (thanks mlocati)
* Remove Guest from "Group to enter on registration" options (thanks hissy)
* Fixed: Copy page does not change the mpRelationID of the new page (thanks 1stthomas)
* Fixed error with user attribute not calling its method on the correct user object, leading to strange results (thanks deek87)
* Fixed: If you dropped an image into the rich text description of an FAQ entry, when you went back to edit the entry, the image didn't show up (thanks JeRoNZ)
* Fixes error where Download file does not show up for files that aren’t images (thanks MrKarlDilkington.)
* Fixed: $c->getPageWrapperClass() removes all other specified classes (thanks HamedDarragi)
* Fixed: UI: Can not select topic in large tree on Page Search (thanks hissy)
* Fixed error in Redis cache backend: Password set in config is not sent Redis connection process (thanks HamedDarragi)
* Fixed untranslated text in the Event List block (thanks iampedropiedade)
* Fix showing empty error message when a problem occurred using Setup on Child Pages (thanks HamedDarragi)
* Fixed error where bumping the concrete5 version number without changing a version\_db number wouldn’t re-trigger an upgrade.
* Fixes issue with broken links to files in textarea(richtext) attribute  (thanks dimger)
* Check $search\_path is set and string in search block view (thanks r-kumazaki)
* Fixed errors in full page caching under multisite setups. (thanks ahukkanen)
* Fixed errors in full page caching with blocks that used special parameters – the page was saved properly but it would replace the contents of the pages without parameters (thanks ahukkanen)
* Fixed: 8.5.2RC1 - Adding external link with URL "/" breakes the whole site (thanks mlocati)
* Fix error on delete user who has express enties (thanks hissy)
* Fix: calendar feed parameter and validation (thanks myq)
* Fixed: Calendar events displayed only on starting month when they span multiple months (thanks cirdan)
* Fixed bug with rich text editor not exporting content properly (thanks ahukkanen)
* Fixed bug where we displayed an error when browsing directly to /dashboard/system/environment/entities/update_entity_settings (thanks mlocati)
* Fixed bug where users who first created would be deactivated if automatic deactivation based on last login were turned on and they hadn’t yet logged in yet.
* Fixed: blocks added to stacks that use JavaScript or CSS assets in their view templates were not working when the block was cached.
* Fixed errors in localization class not including the Config class (thanks haeflimi)
* Fixed login error complaining about Groups being a reserved word under Percona MySQL 8.0 (thanks macserv)
* Fixed issue where in page list block, missing input validation results in mysql-error (thanks krebbi)
* Fixed: Default Express Entry List search functionality does not allow for searching for multiple fields simultaneously (thanks suuuth)
* Fixes bug where Express form answers were emailed in a random order, rather than in the order they displayed in the form (thanks joe-meyer)
* Login page will now no longer let you render parts of authentication type forms if those types are not enabled.
* Fixed bug where images or files added to front-end forms wouldn’t be included in the email notification about those forms.
* Fixed bugs and cleaned up code in the Workflow classes (thanks mlocati)
* Prevent leading/trailing commas from triggering errors in Legacy Form block (thanks MrKarlDilkington)
* Fixed bugs when arranging stack proxy blocks in pages as a non-super user with advanced permissions enabled (thanks mlocati)
* Blocks no longer remain in their target area if there was something about the move operation that failed (thanks mlocati)
* Fixed multiple bugs when working with the HTML Upload interaction type in the image/file attribute (thanks mlocati)
* Fix the layout of the search fields in "Page Report" page (thanks shahroq)
* Fixed: Migration to ut8mb4 incomplete due to problems with schema (thanks mlocati)
* Fixed bug where the hovering image in a file manager window didn’t disappear when clicking on the image record (thanks mlocati)
* Fix inability to connect to marketplace on sites behind SSL when that site is also behing a proxy like Cloudflare (thanks mlocati)
* Fixed: All Day Events are not determined correctly (thanks haeflimi)
* Fix calendar block issues with all-day events (thanks biplobice)
* Fixed inconsistencies when using Ctrl key to deselect images in the file manager (thanks mlocati)
* Fix some issues installing content with the content XML format by disabling request cache during XML installation (thanks mlocati)
* Fixed Issues when removing Custom Workflow Types (thanks deek87)
* Fixed Issues when adding Workflows that have custom workflow types. (thanks deek87)
* Refactored Workflow Types Class to use newer code. (thanks deek87)
* Upgrading jQuery UI to 1.12.1 and downgrading jQuery to 1.12.2 to fix security issue (
* Fixed bug when clicking on folders in Document Library (thanks dimger)
* Fixed: When you add a datetime attribute into the search form, you'll get a JavaScript error.
* Fixed: When paging through versions in stacks or on a page, clicking version doesn't show menu
* Fixed errors when sorting attributes, inability to sort attribute sets as a regular administrator and not the super user (thanks mlocati)
* Fixed: When opening existing repeated events, selected days were not selected.
* Fixed: Unpublished repeated events get published after deleting part of events.
* Bug fixes when updating a site from 5.7 (thanks deek87, mlocati)
* Fixed warnings when sending mail with the intl extension enabled (thanks mlocati)
* Fixed entity not found exception when retrieving author of a file when the author had been deleted (thanks mlocati)
* Fixed StorageLocationFactory::fetchByName should return an instance (thanks hissy)
* Miscellaneous cleanup in URL Resolver classes (thanks mlocati)
* Fixed null pointer exception when user attempted to view calendars in the Dashboard but didn’t have permission access to the first calendar retrieved (thanks kaktuspalme)
* Bug fixes when upgrading from previous versions of concrete5 (https://github.com/concrete5/concrete5/pull/7837) (thanks mlocati)
* Fixed bug where account menu was floating underneath the concrete5 toolbar (thanks mlocati).
* Fixed problems overriding the Express form context registry (thanks ahukkanen)
* Fix block templates that edit the scope variables within the block view (thanks ahukkanen)
* Fixed bug where default contact form in Elemental wasn’t set to store its form data in the backend, only to email it.
* Fix H1 Report 643442 (thanks hissy)

## Developer Improvements

* Add 'noCountryText' option to Form::selectCountry() (thanks mlocati)
* Check that LIBXML constants are defined (thanks mlocati)
* Render jQueryUI dialog buttons in concrete5 style (see https://github.com/concrete5/concrete5/pull/7588 for example) (thanks mlocati)
* Add CkeditorEditor::outputEditorWithOptions (thanks mlocati)
* Updated Punic library to 3.4 (thanks mlocati)
* Added `app()` global helper method to return an instance of the Application object (thanks rikzuiderlicht)
* Update phpseclib from 2.0.13 to 2.0.21 (thanks mlocati)
* Updated Bootstrap to 3.4.1 to fix XSS issue.
* Added two new events: `on_page_alias_add` and `on_page_alias_delete` (thanks faker-ben-ali)
* changing instructions order to send collection version with updated data when triggering approve page version event (thanks faker-ben-ali)
* Add new DestinationPicker form widget to enable users to specify an object to link to, and get a nice widget instead of having to paste a URL (Thanks mlocati)
* Update composer.json to add PDO ext as dependency for project (thanks gavinkalinka)
* Upgrading Spectrum color picker color palette library to 1.8.0 (thanks mlocati)
* Miscellaneous code cleanup and php documentation (thanks mlocati, biplobice, deek87, 	concrete5russia)
* Update IPLib from version 1.6.0 to version 1.9.0 (thanks mlocati)
* Add native lazy loading and JavaScript lazy loading support to the "html/image" service (thanks MrKarlDilkington)
* Added optgroup functionality to the selectMultiple form helper method (thanks mlocati)
* Force attribute keys to be in one set only during import (thanks mlocati)


# 8.5.1

## Feature Updates

* Added the ability to filter logs by time (thanks biplobice) 

## Behavioral Improvements

* Improved translation of user logging in multilingual environments. (Thanks katzueno )
* Improvements to code quality and reduction in suppressed errors (thanks mlocati)
* improvements to using multiple user selectors on a page; miscellaneous bug fixes to user selector (thanks haeflimi)
* improvements to installation on a cluster where site home page ID may not be 1. (Thanks mlocati)
* Improved file size of app.css; removed unnecessary and broken CSS.
* Simplify the warning when the database does not fully support utf8mb4 (thanks mlocati)

## Bug Fixes

* Fixed error where external form actions were not working.
* Fix Exception already used in CharsetCollation\Manager (thanks mlocati)
* Fixed error where move/copy didn’t work in site map flat view (thanks deek87)
* Fix resuming copy language tree operation (thanks mlocati)
* Fixed inability to run some user bulk actions in the Dashboard.
* Fixed JavaScript error when changing default calendar colors in the Dashboard.
* Fixed error in API where authenticated requests could pass through to read any API route.
* Fix error on package uninstall while remove the package directory is checked (thanks biplobice)
* Hide publish now button on versions of pages when user doesn’t have permission to publish (thanks hissy)
* Make sure custom thumbnails have upscaling enabled (https://github.com/concrete5/concrete5/pull/7697)

# 8.5.0

## Feature Updates

* File Storage Location improvements: added the ability to search by file storage location, added file storage location to the file menu, allows changing file storage in bulk using a progressive operation, prevents deletion of file storage locations if they have files (thanks marvinde)
* Added the User Selector attribute to the core, enabling the selection of users for pages, files and Express objects (thanks haeflimi)
* Much improved logging support: more actions are logged, and you have the ability to specify what log levels you want to keep/discard in the Dashboard. Additionally, Monolog Cascade support means granular logging configuration is available in the PHP config.
* Added date modified to express entries (thanks deek87)
* Added “Author” as a property to Express – the users who create express entries are tracked. Added form field for author property as well.
* Added the ability to specify an HTML Input vs Entry Selector vs. Select2 search autocomplete for association selecting in the Dashboard (thanks hissy)
* Added the ability to filter the Express Entry List block at the block level before the data hits the page.
* Express Entry List block can now be filtered by associations in advanced search on the page (thanks hissy)
* You can now filter block types by searching them when adding blocks in stacks (thanks mlocati)
* Added preview images when mousing over images in the file manager (thanks haeflimi)
* Updated CKEditor from 4.9.1 to 4.10.0 (thanks MrKarlDilkington)
* Added the ability to search a site by any locale in the local selector on multilingual sites (thanks mlocati)
* Added a page changes report that lets users export a full list of versions that have been created during a particular time period.
* Nascent support for the upcoming REST API (defaulted to off.)
* Add ability to configure password requirements in a new Password Options Dashboard page.
* Add ability to keep users from reusing the same password.
* Add ability to automatically log users out after a period of inactivity.
* Added a Dashboard page to control Automated Logout settings that were previously only available by editing PHP config files directly (thanks mlocati)
* Added ability to automatically log out all signed-in users from the Automated Logout page.
* Added a dashboard page to configure trusted proxy IPs (thanks mlocati)
* Show URL of selected page in sitemap selector (thanks mlocati)
* Added an external authentication type based on OAuth2 authorization, allowing one concrete5 site to act as the authentication provider for another.
* Add support to generate animated GIF thumbnails (Requires Imagick)  (thanks mlocati)
* Add “Scheduled” as an option for page searches (thanks deek87)
* Add the ability to automatically deactivate user accounts that receive many failed login attempts
* You now can control whether CSV exports contain a BOM with an Export Options Dashboard settings page (thanks mlocati)
* Added ability for YouTube videos to skip setting a cookie (thanks HamedDarragi and tigerxy)

## Behavioral Improvements

* We have removed the spaces from URLs generated by the topic list block for improved display (thanks JackVanson)
* We now show the types of Express entities being viewed in the Dashboard page header (thanks hissy)
* Show errors when displaying Ajax dialogs fails (thanks mlocati)
* We now remember the state of both sitemaps in the 2-up sitemap interface, instead of just 1 (thanks mlocati)
* Split install steps in smaller chunks for better performance (thanks mlocati)
* SVG images in the image block can now be resized in the image block (thanks dimger)
* When entities that own other entities are deleted in Express their child entities will also be deleted.
* Improvements to the stack panel: you can now drag the entire row (instead of a small handle) and you can click an arrow to expand/collapse the stack (thanks mlocati)
* My Account now honors user attribute sets (thanks marvinde)
* Registration now honors user attribute sets (thanks marvinde)
* Added the ability to sanitize uploaded SVG files (thanks mlocati)
* Improved performance of large CSV exports (thanks mlocati)
* Express Entry Detail block now modifies the title of the page when it’s rendering a detailed express entry (thanks hissy)
* Improvements to drag performance and experience in sitemap (thanks mlocati)
* Miscellaneous improvements to editing external links 
– https://github.com/concrete5/concrete5/pull/7004 (thanks mlocati)
* When deleting an element (express entity, file, page, site, user), the associated row in the index table are automatically deleted (thanks mlocati)
* Uploading files via the Your Computer dialog in the File Manager now has chunking support (thanks joemeyer)
* Fixed error where “stay signed in for two weeks” didn’t work (thanks Xianghar)
* Send a JSON error response only if the client is requesting a JSON response (thanks mlocati)
* When showing changelog updates for packages we now read from CHANGELOG.txt and CHANGELOG.md if they exist (thanks mlocati)
* You can now view SVG images in the file manager like other image files (thanks mlocati)
* Remove frameborder attribute on YouTube block and use CSS border for W3C validation (thanks marvinde)
* Show different text for aliases and external links in removal confirmation (thanks mlocati)
* New and existing databases will be updated to utf8mb4 – adding emoji support! (thanks mlocati)
* Add a version-specific querystring parameter to URL local assets based on core version or the package version (thanks mlocati)
* Improvements and consolidation of different libraries used to upload files (thanks mlocati)
* Added CKEditor Emoji plugin (thanks mlocati)
* Allow sending the registration notification to multiple email addresses (thanks marvinde)
* Fixing issue with Image Editor not adding crossOrigin (thanks deek87)
* Moving Delete all channels button to header to remove ambiguity (thanks joemeyer)
* Use translated text when dislaying checkbox labels with the checkbox custom attribute (thanks mlocati)
* Fixed bug where deleted pages could break uses of the page selector component that referred to them (thanks Ruud-Zuiderlicht)
* We use less memory when uploading and resizing large images in the file manager (thanks mlocati)
* Better validation against unexpected input when filtering page list blocks and page title blocks by months and years (thanks hissy)
* Better error checking against remote files uploaded in the file manager (thanks mlocati)
* Keep animations when ConstrainImageProcessor resizes animated GIFs (only works if you’re using Imagick support in PHP) (thanks mlocati)
* Return the default 404 error page if a feed can't be found (thanks mlocati)
* You can now merge social links as well as append them in config (thanks mesuva)
* We force MyISAM database tables for the PageSearchIndex now only if the MySQL version of InnoDB tables doesn’t support it (thanks mlocati)
* Downloading multiple files with the same name downloads only one (thanks marvinde)
* Added the ability to replace a page with another page (thanks mlocati)
* Update CKEditor from 4.10.0 to 4.11.1 and add Auto Link plugin (thanks MrKarlDilkington)
* Fixed workflow emails showing irrelevant dates in some cases (thanks katzueno)
* Fixed: Group Combination returns wrong group combination if there is another entity contains same group combination (thanks deek87)
* Improved speed when adding files to file sets because we no longer refresh thumbnails on every add to file set (thanks mlocati).
* Fixed incorrect flag showing if a page is aliased from one locale to the next (thanks Ruud-Zuiderlicht)
* Fixing errors in UserList::filterByInAnyGroup (thanks deek87)
* Fix issue where some console commands didn’t have a description even though it had been set in the command class (thanks mlocati)
* Fixed: When using inline blocks, I can edit other inline blocks (thanks hissy)
* (Try to) redirect to the newly generated thumbnail if it's the requested one (thanks mlocati)
* Dashboard page title tags are now translated properly (thanks mlocati)
* Stack In Dashboard leave pop-up menu when adding a content block (thanks mlocati)

## Bug Fixes

* Fixed inability to delete conversation messages from dashboard (thanks hissy)
* Fixed: Unpublished scheduled page gets published when there is a new version with schedule (thanks deek87)
* Fixed: Avoid displaying an empty message when forcing exit edit mode (thanks mlocati)
* Fixed built-in limit of 1920x1080 on some uploads (thanks mlocati)
* Fixed: Automatically resize uploaded images" breaks PNG semi-transparency (thanks mlocati)
* Fixed: User with 'Approve Changes' permission is not able to approve content in global areas (thanks mlocati)
* Fixed: Avoid error on getting users of group permission access entity when group has been deleted (thanks hissy)
* Improved page version publishing date support to ensure that versions cannot overlap (thanks mlocati)
* Fix too many results in PagerAdapter::getSlice (thanks mlocati)
* Fixing Issue when deleting users who created other users (Thanks deek87)
* Fixed bug where a session cookie is always created in a multilingual site, even when it shouldn’t be required (thanks marvinde and mlocati)
* Fixed poor performance when running the search indexing job on large sites where areas are set to use the blacklist indexing method (thanks ahukkanen)
* Fixed: Trying to add a larger number of files to a file set in bulk leads to an out of memory error (thanks mlocati)
* Fixed errors and buggy behaviors in sitemap overlay dialog (thanks marvinde)
* Fixed minor display issues with the page version listing dialog/panel (thanks marvinde)
* Fixed When the Zend I18N component loads language files with wrong or missing plural rules (thanks mlocati)
* Correctly detect if sendPrivateMessage returned an error (thanks mlocati)
* Fixed `Call to a member function getTimezones() on null` on editing profile (thanks mlocati)
* MIscellaneous bug fixes with scheduled pages and 404 experience (thanks deek87)
* Fix ParentPageField search field when page is no (more) available (thanks mlocati)
* Fixed bug where editing an express entry in the Dashboard doesn’t re-show the entry form when validation is failed (thanks ahukkanen)
* Fixed inability to add page type composer output control blocks if you were not the super admin but you still had access to page type defaults (thanks hissy)
* Fixed: Single::addGlobal can create the same single page repeatedly (thanks hissy)
* Fix resizing images on import when only max height is set (thanks mlocati)
* Fixed: Thumbnail error takes down Dashboard completely (thanks mlocati)
* Fixed: we now check more appropriate permissions when checking to see if users have permissions to edit stacks (in advanced permissions) (thanks mlocati)
* Fixed: Deleting attributes used with customized results in advanced search leads to an error (thanks mlocati)
* Fixed: RSS Feed can not be filtered by multilingual parents (thanks mlocati)
* Add CSRF validation token to Copy Languages (thanks mlocati)
* Fixed bug when the site id contained in the ConcreteSitemapTreeID cookie does not match a valid site (thanks marvinde and a3020).
* Fix an error when selecting trash or system pages as the parent page on page search (thanks deek87)
* Fixed: Old draft pages of multilingual site upgraded from 5.7.5.13 to 8.4.x gets error (thanks deek87)
* Fixed bug where users could see certain aspects of others users private messages (thanks mlocati)
* Patch Zend HTTP with security update to fix https://framework.zend.com/security/advisory/ZF2018-01 (thanks mlocati)
* Fixed: Currently when using a userSelector if you search for a user or load a new page and try to use the dropdown to select user(s). The option will disappear. (thanks deek87)
* Fixed: Page Selector with pagination doesn't work (thanks marvinde)
* Fixed bug where exporting forms might put the form data in the wrong columns.
* Fixed: Page version menu doesn't close automatically (thanks joemeyer)
* Fixed: Option for the multilingual canonical URL is not respected (thanks 1stthomas)
* Fixed: https://github.com/concrete5/concrete5/issues/7152 (thanks mlocati)
* Fixed: Block is not being rendered using custom template after editing when custom template was set programmatically (thanks fabian)
* Only parse $_SERVER[‘argv’] on the command line (thanks mlocati)

## Developer Updates

* Completely new routing component with a much nicer syntax for creating custom routes to closures, controllers and other classes, with full support for route requirements, HTTP verbs and much more. (fully backward compatible)
* concrete5 now supports PHP 7.3
* Adding Redis as a Session and Cache handler (thanks deek87 and concrete5 Japan)
* Added the ability to rescan files via a console command.
* Much improved console command, including support for progress bar, Laravel-like syntax definitions and more.
* Improve ability to configure and extend concrete5 session.
* New memcache session handler. See https://github.com/concrete5/concrete5/pull/7258 for configuration information.
* Added an option to control whether or not to display parent page in AutoNav (thanks hissy)
* Allow custom class loading from the package for a custom permission key (thanks biplobice)
* Trigger event when the display order of a page changes (thanks a3020)
* Improved SiteLocaleSelector: show Country in addition to Language, and added new selectMultiple method to the class (thanks mlocati)
* Add a config value to toggle the generator meta tag (thanks marvinde)
* Upgrade Imagine image manipulation library from 0.7.1 to 1.0.0 (thanks mlocati)
* Refactored certain old tools files into routes, views and controllers (thanks mlocati, marvinde)
* Added the ability to automatically include CSS files when adding/editing blocks by including an auto.css file in the block folder (thanks mlocati).
* Image Slider block - remove old CSS and JS assets code (thanks MrKarlDilkington)
* Refactoring and code improvements to CookieJar service (thanks mlocati)
* Improved code quality and removal of PHP NOTICE errors (thanks mlocati, a3020)
* Tons of new docblocks added to core classes (thanks mlocati)
* Fix docblocks in Number service (thanks a3020)
* Improve installation detection by allowing {$env}.database.php 
* Let sitemap event listeners modify the sitemap data (thanks mlocati and a3020)

# 8.4.5

## Bug Fixes

* Fixes a vulnerability which permitted authenticated users to view the contents of arbitrary messages sent through the My Account section.

# 8.4.4

## Feature Updates

* Improvement for compliance and GDPR: Storage of form data submitted through the form block is now optional. It is a new checkbox in the block (thanks Faker Ben Ali)

## Behavioral Improvements

* Much improved performance in the Stacks panel menu for sites with a lot of stacks – stacks lazily load the blocks within them.
* Dashboard Welcome Page: hides the "Customize" button if the user does not have permission to edit the page content (thanks marvinde)
* Allow disabling of Sitemap button in CKEditor concrete5link core plugin (thanks joemeyer)
* Fixed W3C validation errors in Elemental (thanks MPagel)

## Bug Fixes

* Fix XSS error when certain error messages could contain HTML (thanks mlocati)
* Fix error where EditorServiceProvider was complaining about array_merge not being a valid array
* Fixed: GDPR - ConversationMessages are not deleted when a user is deleted (thanks marvinde)
* Fix typo in list of CKEditor plugins ('applying') (thanks a3020)

# 8.4.3 

## Behavioral Improvements

* The word ‘Action’ is now properly localized in in-page notifications (thanks mlocati)
* The icon of external links now more clearly distinguishes them from page aliases (thanks mlocati)
* Create collection handle when aliasing the homepage (thanks mlocati)

## Bug Fixes

* Bug Fix: Tags block - support mixed case tag names when setting selected tag class (thanks MrKarlDilkington)
* Fixed bug where archived notification alerts were showing up in Waiting for Me.
* Fix PHP 7.2 count error in Calendar Dashboard Colors system page (thanks altinkonline)
* Fix Page::movePageDisplayOrderToSibling() when working with aliases (thanks mlocati)
* Fixed incorrectly returning object instead of text string when working with textarea attributes under some circumstances.
* Fixed Exception in Marketplace.php after site/project has been removed from community account
* Fixed accidentally deleting all FileSets when deleting a user (thanks deek87)
* Fix alternative canonical URL not installing properly when set during installation (thanks a3020)
* Fixed: Deactivating users in bulk fails in 8.4.2 when a workflow is attached to the permission.
* Fixed Express Entry association view on owned element when creating elements showing a list of all entries instead of none.
* Fixing permission checker on image_uploading / thumbnail options page (thanks deek87)
* Fix package installer not checking dependencies on other packages (thanks acohin)
* Avoid errors in editing express entry detail block on PHP 7.2 under certain circumstances (thanks hissy)
* Fixed: Datepicker options has no effect in 8.4.2 (thanks alexeytrusov)
* Require pagination asset from express entry list block (thanks hissy)


# 8.4.2

## Feature Updates

* Added filtering and pagination to the Waiting for Me workflow notification list.
* Better unsetting/removal of data when users are deleted (useful for GDPR compliance). More details here: https://github.com/concrete5/concrete5/pull/6693 (thanks a3020)
* Delete unused filesystem files and thumbnails when a file version is removed (thanks mlocati)
* We have removed the Flash-based avatar editor in favor of a JavaScript-based component
* Fix typos in Google Maps API check (thanks mlocati)
* Do not link to non active page in content block (thanks hissy)

## Bug Fixes

* Fixed error linking to marketplace addon and theme pages on the Connected to Community Pages; Fixed inability to click through to marketplace detail add-on or theme pages in the Dashboard
* Fixed inability to download free add-ons through the marketplace Dashboard pages.
* Fixed inability to install new block types via the Block Types Dashboard page (thanks dimger)
* Fixed bug where multiple workflows wouldn’t fire if the user could automatically approve the first one.
* Fixed inability to ctrl-click or command-click file manager results to select them in bulk (thanks dimger)
* Fixed error getting temporary directory when running generate sitemap job (thanks mindhun)
* Fixed: 8.4.0 - An exception occurred while executing 'INSERT INTO UserWorkflowProgress (uID, wpID) VALUES (?, ?)' with params [null, \"25\"]:\n\nSQLSTATE[23000]: Integrity constraint violation: 1048 Column 'uID' cannot be null (thanks dimger)
* Fixed bug in migrating data where sites already had the Page Selector add-on installed, and some attribute values were null (Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException) (thanks mlocati)
* Fixed inability to upgrade to 8.4.1 from 5.7.5.13.
* Fix JavascriptLocalizedAsset::getAssetContents when concrete5 is installed in subdirectory (thanks mlocati)
* Fix infinite redirection visiting existing dirs when seo.trailing_slash is false (thanks mlocati)
* Fixed: Duplicated seo.trailing\_slash definition (thanks mlocati)
* Made it impossible to store XSS in calendar event names.

## Developer Updates

* Lots of code cleanup surrounding username and email validation, added a new Username and Email validator (thanks mlocati)
* Add public properties to next_previous block controller (thanks a3020)
* Add CLI command to refresh database entities (thanks mlocati)
* Updated Translation Library (thanks mlocati)

# 8.4.1

## Feature Updates

* Added the ability to automatically deactivate users based on how long it’s been since they’ve logged in.
* Added the ability to save search presets for users and pages and Express objects. (thanks marvinde)
* Added the ability to sort block types and block type sets in the Dashboard (thanks mlocati)
* Add support for theme-color meta tag in the Basics settings section of the Dashboard (thanks mlocati)
* Allow upscaling images for thumbnails based on thumbnail type (thanks mlocati, jneijt)
* Add tooltips to the plugins listed on the Rich Text Editor page in the Dashboard that describe what they do (thanks mlocati)
* The Page Selector attribute is now integrated into the core (thanks marvinde)
* Added a Draft List block type to the Waiting for Me screen in the Desktop (thanks marvinde)
* Added a command line script to generate sitemap.xml (thanks mlocati)

## Behavioral Improvements

* Reworked Add Content Panel Functionality: Make it so that clicking again on the plus/add panel closes the panel (like all others.), If a user option/clicks the panel when opening it, activate the blue/pinned/locked functionality. Clicking to close the panel closes the panel and removes this functionality (thanks marvinde)
* Use UI localization context in concrete5 toolbar & account menu (thanks mlocati)
* Fixed: Whoops report is confusing the reporting with the original error when adding or updating blocks that fail (thanks mlocati)
* Version approved date is now shown in the approved version panel (thanks marvinde)
* Fixed: Language Switcher's language text should display in their native language (thanks mlocati)
* We now highlight localized stacks that have been created to override global stacks in a multilingual website (thanks mlocati)
* Make marketplace error handling more consolidated and handle timeouts
* Set links color in jquery ui dialogs (thanks mlocati)
* Better support for with MySQL 8 (thanks mlocati)
* Support for multiple Page List blocks on a page (thanks marvinde)
* Fix handling of JavascriptLocalizedAsset URL & path (thanks mlocati)
* Don't try to get package lists when concrete5 is not installed in language-install CLI command (thanks mlocati)
* Reduce concurrency problems in FileSystemStashDriver::storeData (can be a problem when clearing a cache on a high traffic site) (thanks mlocati)
* Added a link to the concrete5 Slack channel on the installation screen (thanks mlocati)
* Added a link to the concrete5 Sack channel in the welcome screen (thanks mlocati)
* Improved performance in route resolution (thanks mlocati)
* Avoid long timeouts when checking the Google API Key in Google Maps block (thanks mlocati)
* Avoid warning in Securimage::check when no captcha token is received (thanks mlocati)
* Add $subject to form email templates to make it easier to customize (thanks katzueno)
* Add option to not create session cookies in multilingual sites (thanks mlocati)
* Changed Redactor to CKEditor in the Conversations Rich text editor
* Add ability to change social network icon via config (thanks goesredy)

## Bug Fixes

* Fixed irritating bug where adding multiple express form controls of the same type in a row would cause an error and require form controls to be added and re-saved before proceeding (thanks JeffPaetkau!)
* Fixed error when trying to login using certain third party authentication types (thanks fabian)
* Fixed: File Manager - Duplicate and blank search presets created when creating multiple search presets without page refresh (thanks marvinde)
* Fixed bug where Next/Previous block might skip pages under certain conditions (thanks gfischershaw, mlocati)
* Fixed: C5 8.4.0 - Unable to select root page (home) when adding a new page in sitemap on a multilingual site
* Specifying the items per page for an express entity now works.
* Fixed: 8.4, File Manager in versions, "Invalid file version" when removing old item (thanks mlocati)
* Fixed Call to a member function generate() on null at index.php/dashboard/extend/update
* Fixed bug resolving proper Multilingual Section from browser locale under certain situations (thanks mlocati)
* Fix HackerOne issue 277479 (thanks mlocati)
* Fixed: Copy page moves cID instead of copy in MultilingualPageRelations table (thanks 1stthomas)
* Fixed Express Bug: Argument 1 passed to DashboardFormContext::setLocation() must be an instance of TemplateLocator, boolean given
* Fixed exception thrown when accessing index.php/ccm/system/accept_privacy_policy directly.
* Fixed: Deleting theme error does not have a method 'getPackageItems
* Fixed out of memory error happening on non-US systems when a broken legacy package is included in the packages directory (thanks mlocati)
* Fixed errors with the Page List block not properly filtering by date options (thanks gfischershaw)
* Fixed 8.4.0RC2 - Search presets cannot be deleted in bulk (as the context menu suggests
* Fix a bug where the file manager's breadcrumb is behind the search form (thanks marvinde)
* Fixed inability to disable CKEditor plugins (thanks mlocati)
* Fix setTrustedProxies for Symfony 3.3.0 (thanks mlocati)
* Fixed: FileFolder::getNodeByName and duplicated folder names (thanks mlocati)
* Fix setting the "required" attribute of the privacy agreement on install page (thanks mlocati)
* Actually add translatable strings extracted from config files to Translations instance (thanks mlocati)

## Developer Updates

* Much improve sitemap.xml generation routine, including better memory usage, better ability for extension, and cleaner code (thanks mlocati)
* General code cleanup (thanks mlocati)
* Add "withKey" feature to configuration (thanks mlocati)
* Add Thumbnail Type events (thanks a3020)
* Fix returning file objects in Exception classes (thanks a3020)
* Added `on_block_output` event (thanks a3020)
* Added a debug option in the Dashboard to report PHP NOTICE errors (thanks mlocati)
* Bring back the setNameSpace() method in ItemList (thanks marvinde)

# 8.4.0

## Feature Updates

* Added ability to specify custom thumbnail types per file sets (e.g. if a file is in the Header file set, the Header thumbnail type will be generated for it, otherwise it will not.) (thanks mlocati)
* Calendar block has new agenda views for year list, month list, week and day (thanks MrKarldilkington)
* Added a System Email Addresses Dashboard page that lets you set the default email addresses – previously this had to be done in config code (thanks MrKarlDilkington)
* Added bulk user commands: activate, deactivate, delete, remove from group and add to gorup (thanks JeRoNZ)!
* If a site is connected to the concrete5.org marketplace, any packages installed on the site will have their language files automatically downloaded from translate.concrete5.org (thanks mlocati)
* Adds search header to express entity selector for selecting express entities against pages, users, files, etc… (thanks sjorssnoeren)
* Added the ability to specify an end date for page publishing.
* Added the ability to delete individual Log entries (thanks marvinde, mlocati)
* Added new “Start Time” option to YouTube block; YouTube block will also respect “Start Time” if specified in the YouTube URL (thanks jlucki)
* Added a new Reset Edit Mode Dashboard page that allows all currently checked-out pages to be checked in and edit mode to be restored on them.
* Updated CKEditor to 4.9.1 (thanks MrKarlDilkington)
* Added a new image slider navigation option in the image slider block: “None” (thanks biplobice)
* Added the ability to edit topic tree names (thanks gutigrewal)
* Added the ability to unapprove an approved version through the versions menu.

## Behavioral Improvements

* We now only set sessions when you attempt to login or use custom session code, in order to reduce the number of sites that set cookies for GDPR.
* Added a data collection notice to installation, added a banner to Dashboard for GDPR compliance.
* Massive improvements to image handling in the core, (thanks mlocati!). Full details found here: https://github.com/concrete5/concrete5/pull/6415	
* ItemList: always included ordered-by columns in select statement (thanks mlocati)
* Folded registration email notification preferences into the System Email Addresses Dashboard page (thanks biplobice)
* Much better localization and translation support in the newly introduced calendar components (thanks mlocati)
* We will now inhibit the execution of automatic updates/installations if one is currently in progress (thanks mlocati).
* Improved support when using MySQL 8 (thanks mlocati)
* Improvements to the interactive installation process defaults (thanks mlocati)
* Fixed errors when the update process may require long time, because of many migrations need to be executed or because a migration requires long time to be executed, and the PHP execution may reach its maximum time limit (thanks mlocati)
* Improvements to the coding of the installation process (thanks mlocati)
* Automatically set maintenance mode during core updates (thanks mlocati)
* Apply nowrap white space on private message box status column (https://github.com/concrete5/concrete5/pull/6350) (thanks biplobice)
* Send 500 code instead of 200 on creating an error response (https://github.com/concrete5/concrete5/pull/6350) (thanks hissy)
* Optimizations to UserList classes and group search (thanks deek87)
* Improvements and optimizations to the auto rotate image processor (thanks mlocati)
* We now return. 404 response when requesting an invalid tool (thanks mlocati)
* Improvements to the update process when the calendar add-on was migrated to the new built-in calendar.
* Fixed: Dashboard Sitemap Tree Deleting items should refresh Trash (thanks marvinde)
* Fixed: In sitemap, when you delete a page, plus sign doesn't appear next to the trash can 'til after page reload (thanks marvinde)
* Do not automatically upgrade the core in maintenance mode (thanks mlocati)
* Fixed: When deleting a layout, the message "Are you sure you wish to delete this block?" is shown (https://github.com/concrete5/concrete5/issues/6289)
* Improvements to SNS authentication, Facebook authentication specifically (thanks biplobice, deek87). More details here: https://github.com/concrete5/concrete5/pull/6018
* Better database encoding when databases don’t use UTF-8 by default (thanks upline-pro)
* Use Selectize for Data Source element select multiple inputs (thanks MrKarlDilkington)
* Removed old unused Newsflow code (thanks mlocati)
* Highlight Default Page Template in Defaults and Output for Page Type (thanks MrKarlDilkington)
* Fixed exception filling logs on invalid file (https://github.com/concrete5/concrete5/issues/6449#issuecomment-366931290) 
* Fixed inability to use theme editor CSS classes in CKEditor when using in the Dashboard and non-pages (Thanks MrKarlDilkington)
* Consider text/plain images as SVG images (thanks mlocati)
* Add block type name to delete block modal message (thanks MrKarlDilkington)
* Actively discouraging certain CLI commands when run as root (thanks mlocati)
* Show different message when public profile option isn't changed (thanks biplobice)
* Added cache to core area layout block.
* Improve performance of file manager in certain editor configurations (thanks hissy)
* Allow layout presets to optionally have no container element defined (thanks MrKarlDilkington)
* Better ADA compliance: adding for=”” attributes to label tags in login forms, forgot password forms, all core attributes and express form attributes. 
* Add aria attributes and title to Social Links block links and icons (thanks MrKarlDilkington)
* The dropdown area on the Add Content menu is now clickable (thanks marvinde)
* Removed useless 'More Details' link from package upgrade page (thanks a3020)
* Help prevent block form and file manager modals from blending in with background page content (thanks MrKarlDilkington)
* Added a link to the concrete5.org privacy policy from the login page where backgrounds are pulled from concrete5.org.
* Fixed some errors searching express objects in the Dashboard in some cases (https://github.com/concrete5/concrete5/pull/6601) (thanks hissy)
* Add alt attribute to generic thumbnail icons to increase accessibility in Document Library block (thanks MrKarlDilkington)
* Fix handling of package dependency errors (Thanks mlocati)
* Suggestion: Stays at draft page after "Save and Exit" on Composer (thanks marvinde)

## Bug Fixes

* Fixed multiple bugs that arose because actually removing a multilingual section via the Dashboard didn’t delete the pages in the site tree.
* Fixed error where full page caching was still connecting to the database.
* Fix block dragging in edit mode – it wasn’t scrolling the page in certain browsers (https://github.com/concrete5/concrete5/issues/6321) (}thanks mlocati)
* Fixed: no longer using client side code for rating messages (https://github.com/concrete5/concrete5/pull/6337) (thanks mlocati)
* Fixed bug in survey block where page the survey was on was missing (thanks marvinde)
* Fix issue where updating page defaults on a multilingual site wouldn't push blocks out to all pages in all locales
* Fixed: Adding file selector to form fails on element with special characters (thanks jneijt)
* Fixed bug where pages duplicated would lose custom block cache settings on the resulting pages.
* Fixes issue when a file with multiple versions is the cursor (thanks deek87)
* Fixed: JS Cache combined with "use strict" breaks core javascript (thanks mlocati)
* Fixed: z-index issue when selecting Calendar Events categories (thanks MrKarlDilkington)
* Fixed bug where pages duplicated would lose custom grid container settings on the resulting pages.
* Add missing folder icon in Document Library block (thanks MrKarlDilkington)
* Fixed Error in core\_area\_layout when activating block cache in 8.4RC2 (thanks mehl)
* Fix error with folder item list returning too many items when filtering by multiple file sets
* Fixed bug where replying to messages when logged in would cause replies to show up multiple times before a page refresh (thanks marvinde)
* Fixed bug where applying custom styles to a global area’s blocks would not refresh those styles without a full browser reload.
* Fixed: we now sanitize the alt text in avatars 
(https://github.com/concrete5/concrete5/pull/6339) (thanks Remo)
* Sanitize output on folder names (https://github.com/concrete5/concrete5/pull/6341) (thanks Remo)
* Fixed error running command line utilities when a concrete5 installation has been updated through the Dashboard.
* Fix missing closing h3 tag in Calendar Event block (thanks hissy)
* Fixed missing CSRF token when deleting a conversation message (https://hackerone.com/reports/87729)
* Warnings when attempting to install concrete5 on a database that will make the table names lowercase (thanks mlocati)
* Fixed: Unmapping a locale page, removes the mapping for all locales (thanks Seanom)
* Fixed: Wrong language used in a single page controller (thanks mlocati)
* Fix H1 309466 (thanks mlocati)
* Better permissions checking on Express entry list results in custom Express objects and Express forms.
* Fixed bug with queues and queueable jobs where one job running might start executing the jobs of another process (thanks ahukkanen)
* Fixed bug where you couldn’t unset a “More Details” calendar event page link in the calendar event edit popup.
* Fixed: Google map - multiple API calls if Check API clicked multiple times (thanks MrKarlDilkington)
* Fixed: Delete user attribute values on user delete (thanks marvinde)
* Removed unnecessary paragraph tags in output of FAQ block (thanks djkazu)
* Fix: https://www.concrete5.org/community/forums/customizing\_c5/8.3.1-symphony-error
* Fixing some cases where exporting form results to CSV could result in a 404 error under advanced and custom permission use cases.
* Fixed: Creating a page alias in another site tree does not modify the siteTreeID
* Sanitize the link of external pages in the sitemap (https://github.com/concrete5/concrete5/pull/6346/) (thanks mlocati)
* Fixed: PageList topic filtering MySQL error (mode ONLY\_FULL\_GROUP\_BY) (thanks mlocati)
* Fixed minor XSS vulnerability in unused $step GET parameter (thanks jordanlev)
* Fixed: "Schedule Publishing" dialogs are not removed when adding page (thanks marvinde)
* Fix locale and language of MultilingualPageRelations when site locale changes (thanks mlocati)
* https://github.com/concrete5/concrete5/issues/6490 (thanks marvinde)
* Fixed Minor Bug: "Move to Folder" in Filemanager and not selecting a target causes exception
* Fixed: Deleting a File Leaves it Selected in Form (thanks marvinde)
* Fixed: Applying a theme to a site in the Dashboard only does it to a single multilingual tree
* Fixed: Unable to add new options to select attribute in composer under PHP 7.2
* Fixed Access Denied bug when editing blocks with validation errors under certain conditions (https://github.com/concrete5/concrete5/issues/6425) (thanks marvinde)
* Fixed: The file manager's breadcrumb appears on the full sitemap page (thanks marvinde)
* Fixed: Possibility to crash calendar event list if number of events is not specified
* Sanitize the output of page short description in the pages panel 
(https://github.com/concrete5/concrete5/pull/6347) (thanks mlocati)
* Fix: area layout using preset not deleted after deleting area layout (thanks mlocati)
* Fix migration to version 8 when MultilingualPageRelations contains invalid data (thanks mlocati)
* Fixed: Unable to decode session object after updating profile information and using database sessions on certain multilingual installations.
* Fix: The file manager's breadcrumb appears on the full sitemap page (thanks marvinde)
* Fixed: Running an advanced search on Express forms can produce error in PHP 7.2.
* Fixed error when upgrading from 5.7 with custom address attribute countries (thanks mlocati)

## Developer Updates

* Add support for the "media" attribute for CSS resources (thanks marvinde)
* Added `on_locale_add`, `on_locale_delete` and `on_locale_change` events (thanks dimger)
* Add `on_block_before_render event` (thanks a3020)
* Old page statistics code has been removed (thanks a3020)
* Add `on_block_duplicate event` (thanks a3020)
* Removed inline JavaScript from Google Maps block view layer (thanks Remo)
* Updated to jQuery 1.12.4 (thanks MrKarlDilkington)
* You can now specify default block templates by a particular page type (thanks haeflimi) (see details here: https://github.com/concrete5/concrete5/pull/6456)
* Added a console command to rerun certain migrations (thanks mlocati)
* Add a configuration key to set the Composer autosave idle timeout (thanks mlocati)
* Update responsive-slides asset from 1.54 to 1.55 (thanks apaccou)
* Add c5:is-installed CLI command (thanks mlocati)
* Updated the fullcalendar JavaScript library to version 3.8 (thanks MrKarlDilkington)
* Updated Punic Unicode library to 3.0.1 (thanks mlocati)
* dispatch a additional event when File Sets are deleted (thanks haeflimi)
* Added phpdoc comments for better API documentation (thanks mlocati, AdamBassett)
* Updated Imagine image procesing library to 0.7 (thanks mlocati)
* Updated Symfony components to 3.4.7
* JavaScript is now fully testable (thanks mlocati)
* Let FileFolderManager filter by file extensions, improve FileManager service (thanks mlocati)

# 8.3.2

## Feature Updates

* Updated CKEditor rich text editor component to 4.8.0 (thanks MrKarlDilkington)

## Behavioral Improvements

* Improvements to coding standards and PHP documentation (thanks mlocati, HamedDarragi)
* Scan the SRC directory within the application for translatable strings (thanks matt9mg)
* Fixed users being able to delete core and active themes (thanks deek87)
* Removal of inline block JavaScript to facilitate more performant websites (thanks Remo)
* Certain text field database indexes will be preserve across the upgrade process, leading to better performance (thanks mlocati)

## Bug Fixes

* Express Entity attribute type was not installed due to a bug in 8.3.0 and 8.3.1. This is now fixed.
* Improvements to the upgrade process: fixes to missing database tables under certain conditions (thanks mlocati)
* Fixed bug where blocks were not having their output added to the output cache, leading to general slowness, and a slow Dashboard Welcome page.
* Fixed fatal error on higher traffic websites complaining about timeouts, broken cache files.
* Fixed: The current "check for updates" dashboard page doesn't report the latest version because of a bug in the cache reading/writing process (thanks mlocati)
* Fixed: Updating preset layouts destroys database structure which can result in severe errors (thanks mehl)
* Fixed: filterByTopic / MySQL 5.7 compatibility (thanks apaccou)
* Fixed bug where Geolocators table wasn’t created when upgrading from 8.2.1.
* Fixed: Page duplicated from Versions menu doesn't contains IsDraft state, gets published under drafts.
* Fixed http://www.concrete5.org/developers/bugs/8-3-1/exception-on-login-page-when-mobile-theme-switcher-is-active-and (thanks JeRoNZ)
* Fixed issue with no blocks displaying on PHP 7.2 (thanks mlocati)
* Fixed Youtube block video issues with showinfo and loop (thanks deek87)
* Removed stray </li> tag in topic list block view template (thanks JeRoNZ)
* Fix directory name in extract package strings (thanks hissy)
* Fixed: Form submission notifications throw an error on the Waiting for Me page if the form data object is deleted. 

## Developer Improvements

* UserSelector::selectMultipleUsers can now accepted square brackets in its name, enabling it to be used with custom attributes (thanks mlocati)
* Move the post-login URL management to a service class (thanks mlocati)

# 8.3.1

## Feature Updates

* Added support for upgrading from older versions of concrete5. Now you may upgrade from 5.7.5.13 all the way to 8.3.1, and from any version in between.
* Added the ability to search form results in the Dashboard.
* Added support for importing and exporting Express entities and their entries to the Migration tool. 
* Added the ability to sort by custom display order to the Express Entry List block (thanks gutding)

## Behavioral Improvements

* Delete empty global area record when clearing cache (should speed up a sure) (thanks remo)
* Add more information on workflow notification popup window (thanks hissy)
* Code cleanup and improvements (thanks mlocati)
* Miscellaneous code cleanup (thanks mlocati)
* Multilingual sitemap now remembers which tree you were viewing last, will open to that language in Dashboard Sitemap.
* Improvements to pages panel sitemap when used in a multilingual site.
* Added a link from a form results Dashboard view over to its Express data object editor in the system and settings page.
* Improvements to block/area box-shadow styling when using the design editor (thanks mnakalay)
* Do not allow folder names to be null in file manager (thanks deek87)
* Simplified the public registration settings form in Dashboard (thanks biplobice)
* Moving and updating files in the file manager will now update the modification date of the containing folder (thanks deek87)
* Made file inspectors more robust so that broken images or other issues don’t cause them to die (thanks mlocati)

## Bug Fixes

* Fixed bug where block action URLs for blocks in global areas would not work, leading to an inability to edit bugs like the Express Form when the block is in a global area.
* Fixes #6135 when editing a survey block would delete existing options (thanks mnakalay)
* Fixed: When adding new options to existing options in a survey block, they are saved with a display order starting at 0 so the order is not respected (thanks mnakalay)
* Fixed: Next/Previous showing unapproved pages (thanks deek87)
* Fix: All drafts or no drafts are listed in "Add Pages and Navigate Your Site" panel (thanks hissy)
* Fixed bug where publishing pages in composer using in-page sitemap wouldn’t show languages in a multlingual site.
* Fixed: Dashboard's Update pages has been moved, and now link is still unchanged and get 404 (thanks katz)
* Fixed bug where blocks that register view assets (like JS and CSS that they require) do not output those assets when the block is pasted throughout the site using the clipboard (thanks Ruud-Zicherlicht)
* Fixed bug where errors could occur when submitting an Express Form with incomplete values (failing validation) and having an option list attribute in the same form.

# 8.3.0

## Major New Features

* The core team’s Calendar add-on is now available in the core! It’s much improved from the version in the marketplace. It includes:
* The ability to add multiple calendars to your site
* Join pages to calendar events
* Calendar events are a separate data model from pages.
* Custom attributes on calendars
* Event List, Calendar and Calendar Event blocks
* Calendar and Calendar Event custom attributes.
* Detailed permissions at the calendar level.
* Workflow integration with calendar events.
* Version control for calendar events (!)
* A powerful recurring event model that works even with event versioning.
* Additionally, the core team’s Document Library add-on is now available in the core! The Document Library add-on lets you easily place a list of files on the front-end of your website. Filter by folder or file set, provide a simple search interface, control the styling of results and more.

## More New Features

* New GeoLocation Framework available, along with an included plugin from geoPlugin); geolocate site visitors and get information about where they’re coming from. Ability to automatically populate address attributes from geolocation information (thanks mlocati). More here: https://github.com/concrete5/concrete5/pull/5837
* New command line utilities to clear IP blacklists, and dialogs to do the same (thanks mlocati)
* You can now edit multilingual locates you add through Multilingual Setup (thanks mlocati)
* Conversation block - toggle display of social sharing links and code update (thanks MrKarlDilkington)
* Added the ability to customize CKEditor toolbar groups via the configuration file, without overriding PHP classes. An example of a customized config file that controls editor/toolbar groups can be found here: https://gist.github.com/MrKarlDilkington/5a14cf2c8aca511c8c9d2026e07b297c (thanks MrKarlDilkington)
* Added the ability to turn the Select attribute (now called “Option List” into a list of radio buttons.)
* Mobile Dashboard menu now includes subpages (thanks MrKarlDilkington).
* Improved appearance of CKEditor rich text editor; now closer to concrete5’s UI (thanks MrKarlDilkington)
* Allow users to add <meta rel="canonical"> tags to site pages (thanks mlocati)
* Make username and confirm password display/hide configurable for registration form from dashboard (thanks biplobice)
* Improvements to CSV export and import of data.

## Behavioral Improvements

* Added the ability to search by users not in a group to the Dashboard user search interface.
* Added the ability to see the date of last login to the Dashboard user search interface.
* Added an icon to notice level logs in the Dashboard logs interface.
* Added logging into cache clearing.
* Added ability to open links in Image block in a new window (thanks a3020)
* Add date created to csv export for express entities
* Feature block: increase the preview icon size (thanks MrKarlDilkington)
* Let users configure the thumbnail generation strategy via UI  (thanks mlocati)
* Thumbnails for PNG images are now PNG files and not JPEG files (thanks mlocati)
* UI tweaks and code improvements to External Form block (thanks MrKarlDilkington)
* Add option to retain thumbnails when clearing cache from command line (thanks mlocati)
* Cosmetic improvements to upload dialog (thanks andoro)
* Show current language when showing when showing hreflang (https://github.com/concrete5/concrete5/pull/5868) (thanks Remo)
* Reset answer type form after adding question (thanks Remo)
* PageList and Page List block - sort pages by date modified (thanks MrKarlDilkington)
* Removed exception throwing from invalid SQL order by provided by user – instead it will be ignored.
* You can now search multilingual trees through the page search interface in the Dashboard.
* Retina/High DPI thumbnails are now controlled via config value that can be disabled (thanks Remo)
* Improve image rendering in ImageEditor for browsers that supports it (thanks mlocati)
* Make Basic Workflow Notification From Address and Name configurable (thanks katz)
* Fix position of dropdown menu in blacklist dashboard page (thanks mlocati)
* Miscellaneous small performance improvements and optimizations (thanks mlocati)
* Better error message when saving attributes (thanks mlocati)
* Fixed package restore after failed package update (thanks mnakalay)
* Refactoring and cleanup of installation process (thanks mlocati)
* Add Pager Pagination page number (thanks MrKarlDilkington)
* File manager is now more mobile friendly.
* Improvements to the date attributes custom text mode setting (thanks mlocati)
* captcha improvements https://github.com/concrete5/concrete5/pull/6036 (thanks mlocati)
* Allow customizing the headers of the email attachments (thanks mlocati)
* Hide block and area design features if disabled (thanks Remo)
* Much better performance when grabbing page drafts on a live site.

## Bug Fixes

* Fixed bug where cache directory and thumbnail cache was cleared any time an override cache was cleared. (Note: this fixed an issue with the new asynchronous thumbnail generation strategy that left thumbnails unable to rebuild.)
* Dashboard mobile menu works again.
* Fixed user account menu not showing account operations like Edit Profile, Edit User Picture unless the user was a user with access to the dashboard.
* Fixed issue when using the Page Selector and choosing an alias the original would be selected instead (thanks Ruud-Zuiderlicht)
* Fixed: Survey Dashboard page broken.
* Fixed: Empty file & image blocks get exposed when block cache is enabled after quitting edit mode without doing anything
* Fixed bug where topic order wasn’t being saved properly in the topic trees (thanks deek87)
* Fixed bug where new drafts had the locale of the default site tree, in multilingual sites. Fixed bug where they could not be duplicated into a new part of the site properly.
* Fixed checkbox attribute not honoring settings when editing attributes with values.
* Fixed: Error on file\_manager\_detail thumbnail creation (no height set on installation.)
* Fixed: Saving and re-editing content won't allow you to edit links (thanks mnakalay)
* Fixed bug where searching express entities by a many association wasn’t selecting the entries on returning to the form.
* Fixed: Multilingual redirect based on browser locale not always working (thanks fabian)
* Fixed bug where CSS and JS provided by block view templates was wrong in certain situations (thanks mlocati)
* Fixed bugs where thumbnails were removed from the cache directory even when that setting wasn’t checked (thanks mlocati)
* Fixed inability to search in “all pages” in Dashboard Page search in a particular multilingual site tree.
* Fix the site tree filter of MultilingualPageList in multilingual/page\_report (thanks mlocati)
* Fixed in ability to create page from multilingual page report (thanks Remo)
* Fixed http://www.concrete5.org/community/forums/internationalization/multilingual-site-error-after-upgrade-to-8.2 (thanks mlocati)
* Fixed inability to post results to a different page when using the search block (thanks mlocati)
* Fixed: Editing Express Entries uses the default view form instead of the edit form.
* Snippets in CKEditor work again (along with improved performance) - thanks mnakalay
* Fixed bug in Express where entities listed in an association could not be clicked into from associated entities.
* Fixed: Conversation block generates ccm\_addHeaderItem error when not logged in
* Fixed error when adding attribute from a package into a Form block.
* Prevent uncaught type error when editing links in CKEditor (thanks MrKarlDilkington)
* Fix multiple files showing up when browsing folders in the file manager as the non admin user.
* Fixed: Global Password reset process fails when email registration is enabled (thanks biplobice)
* Fixed possible errors when rescanning files are stuck in the queue and they no longer exist.
* Following an expired Forgot password token no longer gives you a message about it being an ‘Unexpected Error’ (thanks biplobice and katz)
* Fixes a bug with using Group Sets in the "Approve or Deny" permission on the Workflows settings screen for a workflow (thanks justbane)
* Fixed: When duplicating a file, two copies of it gets created (thanks mlocati)
* Fixed possible XSS in stored URL locations dialog (thanks bl4de)
* Fixed: When we adding a new Storage Location that's set as as the default one, we currently end up having two default storage locations in the database (thanks mlocati)
* Image Block: checkbox formatting and prevents the "Open link in new window" value from always being true (thanks MrKarlDilkington)
*Fixed: FAQ block: Entries with " are not properly saved (thanks MrKarlDilkington)
* Fixed: Upgrade 5.7.5.13 to 8.2.1 fails on duplicate key (thanks Ruud-Zuiderlicht)
* Fixed error message “Unable to get permission key for view\_edit\_interface” showing up when an invalid block was specified in an edit interface.
* Fixes duplicating a duplicated file in a folder (thanks Mnakalay)
* Fixed bug where duplicated files weren’t duplicate thumbnails (thanks mnakalay)
* Fixed bug where CSV files exported from Express sometimes didn’t have a filename (only an extension) (thanks toesslab)
* Fixed issue with existing ratings not being populated in edit mode (thanks ggwc82)
* Calls to getContents (a wrapper for the HTTP client) now honor the $timeout argument (thanks mnakalay)
* Faster file rescan when using image constraints (thanks mnakalay)
* Prevent image upload resizing of SVG files (thanks MrKarlDilkington)
* Fixed: It is not possible to make default / main language invisible for a group and show another language sitemap
* Fix saving "thumbnail is built" in ThumbnailMiddleware (thanks mlocati)
* Fixed bug with  uncaught exception in authentication types.
* Fixed: Adding a new page via the sitemap with a required user prevents the page from being created
* Fixed bug where folders and files were showing up as translatable in translate site interface.
* Fixed bug where concrete5 couldn’t be installed on versions of PHP 5.5 before 5.5.21.
* Fixed: Disable intelligent search for marketplace when setting warrants it.
* Page Templates can now be uninstalled from packages that install them (thanks mlocati)
* Show only accessible languages in switch\_language blocks (thanks mlocati)
* Fix to allow strings to be passed to getThumbnail method (thanks deek87)
* Fix clearing cache but keep thumbnails on Windows (thanks mlocati)
* Fixed https://github.com/concrete5/concrete5/issues/5798
* Incorrect CSRF token validation no longer throws an exception in the legacy form.
* Miscellaneous bug fixes to asynchronous thumbnail generation strategy.
* https://github.com/concrete5/concrete5/pull/5968 (thanks mlocati)
* Fixed: Avatar upload should use global jpeg quality settings
* Fixed: File Manager - Advanced search Customize Results don't persist
* Fixed: Password url lifetime doesn't work for different hash type (thanks biplobice)
* Fixed: File Manager - Replaced files are not resized to match the image uploading resize dimension
* 
* Fixed display bug when editing conversation messages.
* fix inline edit detection for blocks pasted from the clipboard (thanks Remo)
* Fixed: Upgraded concrete5 caused duplicated results of topic filter (thanks biplobice)
* Miscellaneous content exporter fixes (thanks mlocati)
* Fixed inability to hard code a block’s custom template in a theme template file and provide that custom template in the theme. 
* fixes bug where fill records were orphaned when deleting a file set. (Thanks Ramonleenders) 
* Fix hacker One report #243865
* Sanitized display value for file nodes
* Prevent XSS in group badge description
* Fixed User date attribute can cause error on profile page
* fixed: When trying to save an edited video block you get the error An invalid form control with name='width' is not focusable.(thanks rikzuiderlicht)
* fixes filterByBlockType on PageLists so that it works with strict versions of mySQL. (Thanks deek87)
* Fix W3C HTML Validator Error for Meta Canonical (thanks appacou)
* Fix possible self-xss on installation screen.
* Better conversation message sanitization when using the rich text editor conversation editor.

## Developer Updates

* Added the ability to specify package dependencies in a package controller (thanks mlocati)
* Updated Laravel Config dependency to 5.2.x. 
* Improvements to command line/composer integration in Windows (thanks mlocati)
* Lots of minor updates to third party libraries.
* Simple syntax for obtaining an error message by field: https://github.com/concrete5/concrete5/pull/5939 (thanks biplobice)
* Support for handling multiple entity managers in a package (thanks mlocati)
* Add support to foreign keys in attribute index tables (thanks mlocati)
* Content Interchange Format can now associate attribute categories to existing attribute types (thanks mlocati)
* Allow converting an error list to plain text (thanks mlocati)
* Added API methods for easily adding a country and state/province selector (used by the address attribute type.) (thanks mlocati)
* Fixed namespace when generating migrations (thanks Remo).
* raise event when page not found is shown (thanks Remo)

## Backward Compatibility

* Captcha updates make affect backward
Compatibility. ( https://github.com/concrete5/concrete5/pull/6036)

# 8.2.1

## Feature Improvements

* Added search to the Express Objects Dashboard interface.
* Added associations to Express Object Listing Interfaces
* Updated CKEditor to 4.7.1 (thanks MrKarlDilkington)
* Added the ability to specify multiple attributes in a mask format for listing attributes in associations in Express. (e.g. %first_name% %last_name% to populate the entity dropdown.)
* Added the ability to open a link in a lightbox once again (thanks mnakalay)
* Improved viewing of videos in the file manager (thanks deek87)
* Improved performance and memory usage when importing images (thanks mlocati)

## Bug Fixes

* Fixed: Page List block pagination displays as "Previous" and "Next" when logged out 
* Stack improvements on upgrade from 5.7 to 8.2 on a multilingual site (thanks mlocati)
* New asynchronous thumbnail generation was passing height along twice, instead of width and height. This is now fixed. (thanks danklassen)
* Fixed bug where incorrect primary key definition lead the Express Entry Detail block to not save properly.
* Fixed: Search block pagination isn't working
* Fixed bug where Express Entity Selector wasn’t working.
* Fixed SQL injection in file folder parameter accessible to logged in users (
* Pagelist update so the topic tree choice affects the preview pane (thanks seanom)
* Fix inability to search pages, users or files in advanced search by boolean attributes
* Fixed Multilingual: Navigate Sitemap does not reflect language
* Added permissions to user lists
* Fixed bug where remote update wasn't able to retrieve information about upcoming releases.
* Fixed: Prevent infinite loop in Next/Previous block under certain conditions.
* Fixed bug with page aliases displaying many times in the sitemap.
* Fixed bug where FileList items repeating in pagination results, pagination doesn't appear
* Fixed miscellaneous permissions errors when updating certain sites from 5.7 to 8.2. (thanks Ruud-Zuiderlicht)
* Fix: Wrong icons for sort order of files in file sets (thanks deek87)
* Fixed: New optional asynchronous thumbnail builder does not load underscore JS. (thanks Seanom)

# 8.2.0

## New Features
 
* Major improvements to language support, including the ability to dynamically download translation languages during installation or at any point afterward (thanks mlocati, Remo, ahukkanen)
* Thumbnail options Dashboard Page: specify whether to keep thumbnails in the PNG format if they are PNG files; provide ability to use Imagick for thumbnailing; better thumbnail functionality behind the scenes (thanks mlocati)
* Added a crop option to custom file manager thumbnail types: now you can specific a width and a height, but still resize items proportionally (thanks mnakalay)
* Added new options to the Date/Time attribute for configuring whether the attribute defaults to the current time, specifying time intervals and more (thanks mlocati)
* Much improved date/time support under the hood (thanks mlocati)
* Autorotate image on upload based on Exif Metadata if concrete.file\_manager.images.use\_exif_data\_to\_rotate\_images is set to true (thanks HamedDarragi)
* File, user and page searches now have the ability to set the number of results in the Advanced Search dialog.
* Multilingual sites now can use a dual sitemap to copy pages from one language tree to another.
* Completely reworked and updated IP banning functionality, including bug fixes, formatting and display improvements, and support for IPv6 addresses (thanks mlocati)
* You can now move files (singular and in bulk) to folders using an overlay window rather than just clicking and dragging (thanks mnakalay)
* Add possibility to see unvalidated users in the user search (thanks simoroshka)
* Added ability to jump to a particular folder in the file manager.
* Improvements to user workflows, including the showing of workflow notifications in the Users section of the Dashboard, and user activation workflow (thanks ahukkanen)
* Users can now be exported as CSV once again.
* Report entries can be exported as a CSV list.
* All Express entities can have their entries exported to CSV lists.
* Added the ability to manually resend validation email to unalidated users (thanks 	simoroshka)
* Allow selection of default folder for uploads when using form block
* Added the ability to specify a custom DOM element ID in the custom style panel (thanks MrKarlDilkington)
* Quick search results are now displayed in the proper locale for the logged-in user (thanks simoroshka)
* Added the ability to specify whether a topic attribute should allow multiple topics to be selected or just one (thanks hissy)
* Added more size options to the Video Player block (thanks MrKarlDilkington)
* Added SVG support to the Image block (Thanks MrKarlDilkington)
* Added Link to File option to the Image Block (thanks MrKarlDilkington)
 
## Behavioral Improvements
 
* Much improved performance in list views (including the file manager) on large sites.
* Style improvements to Survey Block edit Dialog (thanks MrKarlDilkington)
* Style improvements to Surveys Dashboard page (thanks MrKarldilkington)
* Improved localization support when running concrete5 in multiple languages with editors in multiple languages (thanks mlocati)
* Improved block edit dialogs (thanks MrKarlDilkington)
* concrete5 can now load languages without a locale (thanks mlocati)
* Swapped out specific curl calls to a new generic HTTP Client library (thanks mlocati)
* Style improvements to miscellaneous settings Dashboard pages (thanks MrKarlDilkington)
* Added “Sitemap Reverse Order” back to AutoNav block settings.
* Style improvements to editable attributes (thanks MrKarlDilkington)
* When adding multiple folders to file manager the value is cleared and the input element gets focus (thanks xtephan)
* User-focused pages like account, Dashboard pages should use the user’s language if it’s specified (thanks mlocati)
* Improvements to authentication in profile when using the community authentication type (thanks mlocati)
* User attributes are now displayed in set order and in the proper set in multiple places (thanks simoroshka)
Added support for association labels to populate the mask replacement string (thanks aghouseh)
* File deletion is now wrapped in a transaction for better error protection (thanks Mnkras)
* Searchable attributes are displayed in their sets and in set order in the advanced search dialog (thanks AnnaKruglaia)
* Added user specific translations to workflow emails (thanks deek87)
* Make saving associations work when handles are not unique
* Remember dashboard scroll position when navigating the dashboard (thanks mlocati)
* We now Load site interface translation by default (if it exists) (thanks hissy)
* Fixed localized date formatting issues in certain cases (thanks ahukkanen)
* Theme::getThemeEditorClasses now supports all the options defined here: http://docs.ckeditor.com/#!/guide/dev_styles (thanks hissy)
* Fixed broken Express Entry Details block.
* Improved memory usage when rescanning/importing multiple files (thanks hissy)
* Fixed: We don't delete search index table after deleting an Express object (thanks Mnkras)
* Fixed Sitemap flat view problems with multilingual sites
* Improved display of Facebook authentication type form (thanks mlocati)
* The underlying file manager storage location API is now cached (thanks mnkras)
* Miscellaneous formatting improvements (thanks MrKarlDilkington)
* Tags block - add class to selected tag on tag filtered pages (thanks MrKarlDilkington)
* Share This Page block - open links in new window (thanks MrKarlDilkington)
* Fix SVG thumbnails and "Invalid file dimensions, please rescan this file." error (thanks MrKarlDilkington)
* Improved performance loading translations via javascript (thanks mlocati)
* Fix: In case users registered with OAuth, we don't have a way to set the default attribute values (thanks mlocati)
* Fixed error when exporting objects that had a date/time attribute value set.
* Improved design of private messages account page (thanks mlocati)
* Page Search: include system pages when parent id is also a system page (thanks hissy)
* You can now send multiple emails per connection to the SMTP server (thanks mlocati)
* Made it so you can’t drag the guest, registered users or administrators groups in the Dashboard (thanks mlocati)
* Switch Language block now works with single pages (thanks Remo and mlocati)
* Conversation block form - display the Custom Date Format input conditionally (thanks MrKarlDilkington)
* Evenly space the time picker separator (thanks MrKarlDilkington)
* Better styles for permission details list items and checkboxes (thanks MrKarlDilkington)
* If a canonical URL and redirect to canonical URL is set and full page caching is enabled, pages will still be redirected to the canonical URL (which used to not be the case.)
* Authentication types are now translateable (thanks mlocati)
* SEO improvements to the Switch Language block (thanks mlocati)
* Fix Fix - Cancel button event, for who doesn't have public profile (thanks biplobice)
* Fixed http://www.concrete5.org/developers/bugs/8-1-0/upgrade-from-5.7.5.13-to.-8.1-error/ (thanks mlocati)
* Full page caching will now be bypassed on POST requests.
 
## Bug Fixes
 
* Fixed bug where creating a multilingual section made it inaccessible until permissions were manually applied to it.
* Fixed bug where page list only returned pages in the default locale on a multilingual site.
* Fix an issue where concrete.permissions.forward_to_login didn't work (thanks mnkras)
* Fixed package translations not loading in some cases (thanks mlocati)
* Fixed bug where registration approval appeared to be stuck on approve if you ever made it manual and then made it automatic. 
* Fixed Bug: Calendar pop-up of date attribute edit window of file manager goes behind (thanks mlocati)
* Fixed problems with global password reset (thanks Mnkras)
* Fixed bug where users, pages and file searches wouldn’t preserve search as the user paged through the results (thanks AnnaKruglaia)
* Fixed bugs with hierarchical groups and checking whether users were in a group, getting group members, checking permissions, etc…
* Minor display fixes in stacks interface.
* Tons of minor aesthetic and style improvements (thanks MrKarlDilkington)
* Bug fixes with white labelling background URL (thanks SnefIT )
* Fixed Copied Blocks Do Not Recognize Custom Page Theme Classes
* Fixed bug when editing file attributes after upload or in bulk (thanks mlocati)
* Fix unable to search pages from sitemap (thanks hissy)
* Fix https://www.concrete5.org/developers/bugs/8-1-0/form-reply-to-not-working (thanks craveitla)
* Fix wrong message when the session invalidated (thanks hissy)
* Fixed Youtube block not respecting the related video setting (thanks nmakalay)
* Fixed https://github.com/concrete5/concrete5/issues/5366
* Better support for composer editable home pages
* Fixed error that ocurred when editing page properties if the user didn’t have access to user search
* Fixed inability to upload files with a multibyte filename through the dropzone area of the file manager (thanks hissy)
* Fix the URL of the "Reply to private message" page (thanks mlocati)
* Bug fixes with page templates included in packages (thanks apaccou)
* Fixes for minor output sanitizing reports from hackerone (thanks Mnkras)
* SEO panel counter display fix (thanks MrKarlDilkington)
* Fixed https://www.concrete5.org/developers/bugs/8-1-0/translation-file-missing-concretejsi18nui.datepicker-pt.js/ (thanks mlocati)
* Prevent errors when SVG images are used with the Image block (thanks MrKarlDilkington)
* Fixed Format of Date Properties in Page Attribute Display Block not working (thanks magnolia4 and jonkratz)
* Fixed: Unable to use Group Combination Permission Entity to workflows
* Fixed https://www.concrete5.org/developers/bugs/8-1-0/js-bug-empty-sidebar-after-customizing-theme/ (thanks bitterdev)
* Increase regex performance in in HTML block controller method xml\_highlight (thanks mattrice)
* Bug fixes with saved file search (thanks mlocati)
* Fix: deleting CONCRETE5\_LOGIN cookie on sign out not works (thanks hissy)
* Pagination in "core\_conversation" block does not include the selected sorting, he use default sort always.
* Fix Drag'n'drop message is not clickable in File upload popup
* Fixed bug where you couldn’t remove files when they were attached to express entities (thanks Mnkras)
* Fixed https://www.concrete5.org/developers/bugs/8-1-0/urls-and-redirection-and-apache-2.4.10/ (thanks mlocati)
* Fix Multilingual: Browser language detection doesn't work (thanks mlocati)
* Fixing bug with the in-page sitemap selector form helper (should fix issues with selecting pages under certain composer situations, third party add-ons.)
* Fix search users by group set (thanks mlocati)
* Fixed 404 when adding an additional page path with a trailing slash
* Fix bug causing selected topics to be removed on subsequent edit (thanks xtephan)
* Fixed misnamed Image/File attribute type options form (thanks biplobice)
* Fixed Cannot change "Assign Permission" in Full Sitemap page more than twice (thanks deek87)
* Fixed Express: Foreign key constraint validation issue when trying to remove entry
* Resolved https://hackerone.com/reports/238271 (thanks Mnkras)
* Fixed occasional dashboard panel stickiness problem when accidentally closing and then opening the dashboard panel (thanks mlocati)
* Random passwords generated when passwords are reset are more secure (thanks Mnkras and hackerone user ‘plazmaz’)
* Fixes to URLs and Redirection - warning and placeholder (thanks MrKarlDilkington)
* Fix https://www.concrete5.org/developers/bugs/8-1-0/feature-block-ckeditor-source-view-empty-if-no-resized (thanks mlocati)
* Fixed Custom sorting isn't being honored in Express Entry Detail Block
* Bug fixes in Express field set builder API (thanks apaccou)
* Logs - add icon to critical and alert levels (thanks MrKarlDilkington)
* Showing the file title instead of original file name in file folder display
* Fixed some incorrectly set cookies when concrete5 was installed in a subdirectory (thanks Mesuva)
* Fixed https://www.concrete5.org/community/forums/installation/install-error-call-to-undefined-function-doctrinecommonannotatio/ 
* Fixed bug in Date/Time attribute when used with the calendar add-on.
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-8/changing-tags-settings-results-in-deleted-tags (thanks mlocati)
* Fixed https://github.com/concrete5/concrete5/issues/5515
* Added some missed t-functions (thanks concrete5russia)
* Fixed Currently, the "date" widget isn't initialized with the current value: its initial value is always "today” (thanks mlocati, manielsen2002)
* Fixed basic thumbnailer/image block dying when attempting to thumbnail a file that isn’t an image.
* Fixed Express form number attribute does not accept floats in Chrome and other browsers
* Fix default site installed with wrong plural form (thanks hissy)
* Fixed Can't copy&paste advanced permissions to page type (thanks bafrank)
* Fixed problems installing concrete5 in certain languages other than English (thanks mlocati, hissy)
* Fixed error copying and pasting express form 
* Fixed Advanced users search on Express field throws error due to missing method in Express attribute controller (thanks matthabermehl)
* Fixed Update dashboard/users/points/assign.php: The controllers save() method calls an non existent UserInfo method: getByUserID() (thanks danielgasser)
* Fixed Can't delete page attributes in French (thanks mlocati)
* Fixed inability to assign attribute sets to legacy attribute categories (like Calendar add-on).
* Fix unable to edit express entity handle (thanks hissy)
* Fix import of groups without path when using the content importer format (thanks mlocati)
* Fixed inability to fully delete global areas
* Fix unable to use mobile theme (thanks hissy)
* Fix bug when using custom antispam library (thanks Remo)
* Improvements to custom templates when using the Page Attribute Display block (thanks manielsen2002)
* Fixed http://www.concrete5.org/developers/bugs/8-1-0/permission-settings-missing-for-global-areas/ 
* Will no longer try to generate thumbnails based on SVG uploads (thanks MrKarlDilkington)
* Dashboard page consistency/ordering improvements (thanks mlocati)
* Better error handling when thumbnails fail to be written (thanks Mnkras)
* Fixed https://github.com/concrete5/concrete5/issues/5615
* Fixed Recommended FIX for Windows 10 and 2008+ install error due to IPv6 and inet_pton() bug (thanks mlocati)
* Fixed: When I'm trying to access Design & Types for pages like login or register, it generates an error (thanks biplobice)
* Fixed potential XSS error in conversation editor editing (H1 248523)  (thanks bl4de)
* Fixed potential XSS error in private message reply (H1 247517)  (thanks bl4de)
* Fixed for H1 247521 (thanks bl4de)
* Fix for H1 report 248506 (thanks bl4de)
* Fix for H1 report 248504 (thanks bl4de)
* Fix for H1 report 248133 (thanks bl4de)

## Developer Updates
 
* Font Awesome has been upgraded to 4.7 (thanks mlocati)
* Numerous third party components updated to newer minor versions.
* Upgrade Punic to 1.6.5 (fixes installation in some cases) (thanks mlocati)
* Added on_ip_ban event with custom event object (thanks mlocati)
* Miscellaneous code cleanup (thanks mlocati, hissy)
* Added new abilities to require and obtain an SSL URL (thanks mlocati)
* Form Validation Service: add field name to errors (thanks hissy)
* Improvements to the autolink text method (thanks mlocati)
* Added -env option to multiple console commands (thanks mlocati)
* Fix detecting if a page is in dashboard #5208 (thanks mlocati)
* Improvements to the Number Validation Helper (thanks mlocati)
* Added IPLib, a library to handle IP addresses and ranges (thanks mlocati)
* Added addRawAttachment to email helper (thanks mlocati)
* Updated dropzone.js (thanks hissy)
 
## Backward Compatibility Notes
 
* +* Added protected properties to class `Concrete\Core\Application\UserInterface\Menu\Item\Item` in order to avoid accessing undefined properties and optimize memory usage (See: https://github.com/concrete5/concrete5/issues/5307)
* If you have done any Express Form customizations for custom rendering, you will need to update your customizations – there is a new way of performing these customizations that gives greater flexibility and reduces the need for custom templates and spaghetti code. Please check out the Express Form Documentation: [Express Form Theming](https://documentation.concrete5.org/developers/express/express-forms-controllers/form-theming/overview)
* If you have a custom form template for the “express_form” block, you will have to remove the line that looks similar to this at the top of the view template:
    $renderer = Core::make('Concrete\Core\Express\Form\StandardFormRenderer', ['form' => $expressForm]);
* IMPORTANT: If you use the “Manual Approve” method of handling user activations, this option has changed to use User workflows. Add a workflow to the “Activate User” permission to the “Dashboard > System > Permissions > Users” page. This will force users to go through workflow prior to them being approved after registration. **Registration has been disabled on your site!** Once you’ve setup workflow, re-enable user registration from the Dashboard.


# 8.1.0

## New Features

* The Form block can now display output from an existing Express entity object, as well as create a new custom form from scratch.
* Multilingual sites can output <link rel="alternate" hreflang=...> for related pages by setting the site.sites.default.multilingual.set_alternate_hreflang config variable to true (thanks mlocati!)
* You can now hide the footer My Account menu with a setting in the Profiles Dashboard page (thanks mlocati)

## Behavioral Improvements

* Much improved time zone support; fixes a number of bugs, inconsistencies, tests for database and PHP time zone matching (thanks mlocati)
* Updated CKEditor to 4.6; much better CKEditor appearance and button wrapping behavior (thanks MrKarlDilkington!)
* More reliable URL slug generation JavaScript (thanks seebaermichi)
* Make welcome background image cover full width and height (thanks MrKarlDilkington)
* DateTime widget - change default displayed past years from 10 to 100 (thanks MrKarlDilkington)
* Fixed; File Manager Upload does not reflect most recently uploaded files if user doesn't select "View Uploaded"
* Improved thumbnail generation when using the BasicThumbnailer classes – better support for page caching while generating thumbnails, throttling and better performance when generating thumbnails.
* Added toolbar tooltips, defaulted to true but with options to disable in Accessibility settings (thanks seebaermichi)
* Share This Page block now includes full request URI, making it easier to share pages with custom URL parameters (thanks HamedDarragi)
* Image Slider block now includes option for both bullets and arrows (thanks Siton-Design)
* Fixed Resize images client side using 2x downsampling on upload results in jagged images (thanks MrKarldilkington)
* Page Attribute Display block delimiter option works with topics (thanks MrKarlDilkington)
* Add a semi colon to separate JS scripts in cache
* Page Type Form shows its icons at all times, appears nicer (thanks MrKarlDilkington)
* Miscellaneous style improvements (thanks ramonleenders, MrKarlDilkington)
* Escape translations to prevent JavaScript errors because of containing apostrophes (thanks Ruud-Zuiderlicht)
* Upgrade improvements and bug fixes
* When moving a file from one storage location to another the thumbnails will also be moved (thanks Mnkras)
* Increased max amount of size slider (thanks MrKarlDilkington)

## Express Bug Fixes

* Fix success error when submitting Express Form with two forms on a page.
* Fixed bug where Express many to many associations weren’t named correctly, so working with them programmatically didn’t work.
* More reliable deletion of express objects when they have associations to other objects"
* Fixed Express Entities can't be used in a form unless the user is an administrator
* Fixed Script error when express attribute edited in dashboard form results

## Other Bug Fixes

* Removed dummy autoloader added to bootstrap/app.php (shouldn’t affect any applications, but shouldn’t be there anyway.)
* Permissions fixed in the file manager. 
* Fixed incorrect characters displaying when dragging a stack icon (thanks katzueno)
* Fixed Embedding CKEditor in single pages triggers fatal error when CSS and JavaScript Cache is enabled
* Fixed bug where some sites could start rendering -1/ in their paths when editing the home page.
* Fixed double submit bugs when forms or external forms were placed on the home page.
* Fixed errors that would occur when moving or copying aliases
* Fixed http://www.concrete5.org/developers/bugs/8-0-3/404-for-the-dashboard-page-cmsindex.phpdashboardhome/
* Fixed Dashboard file manager menu clipping on in folders without a lot of files (thanks MrKarlDilkington)
* Fix exception being thrown when the workflow requester was deleted (thanks jaromirdalecky)
* Better permissions protection on file manager with File Uploader access entity; better permissions protection on moving files in file manager.
* Fixed PageList::filterByPath returning no pages when working on multilingual sections (thanks OlegsHanins)
* Minor localization issues with Punic calendar library fixed (thanks ahukkanen)
* Fixed File manager file menu does not reflect accurate file after moving files
* Fixed bug where sitemap selector widget didn’t select pages (thanks Mesuva)
* Fixed: Page types with attributes throw errors when copied
* Fixed: Validate Password tokens don’t reset when email is changed (thanks Mnkras)
* Fixed Manual global cache time is displayed wrong on page cache settings (thanks mlocati)
* Fixed delete file storage location ERROR
* Fix filtering of topics in page list block when filtering by topic category
* Fixed FAQ - Delete Entry breaks the Save button (thanks MrKarlDilkington)
* Fixed Invalid block type handle exception during upgrade from 5.7.5.13 to 8.0.3 on sites where the RSS DIsplayer block was removed.
* Fixed: Setting a select attribute default value for page types results in foreign key constraint error in composer
* Fixed: Default Page Attributes do not persist
* Fixed bugs where discarding page drafts might cause page blocks to no longer be editable in composer.
* Fixed: Page Attribute default value not set in composer view
* Fixed exception when dealing with Oauth in bindUser method in some setups.
* Updated Zend Mail component to 2.7.2 to fix security issues.
* Fixed: https://www.concrete5.org/developers/bugs/8-0-3/author-attribute-is-very-tall-when-editing-attributes-from-the-d/
* Added CSRF protection to Forgot Password (thanks Mnkras)
* Fixed Page Attribute - Issue with deleting Rich Text Attribute
* Fix unsanitized file set name displayed in add to sets dialog.

## Developer Updates

* A new search indexing service provider is available, enabling the use of third party search platforms rather than built-in MySQL search for pages. Currently relatively low level and offering our single MySQL implementation, it nevertheless is a good start for adding support for other services like Elasticsearch, Solr and more.
* Developers can implement getPackageTranslatableStrings() in their package controller in order to specify custom strings to add to the translation repository.
* Bug fixes in custom package entity manager configurations (thanks Kaapiii)
* Miscellaneous code commenting (thanks Mnkras)
* Upgrade Monolog to v1.22.0 (thanks mlocati)
* Upgrade Punic to 1.6.4, fixes certain incompatibilities with Symfony Intl.

# 8.0.3

## Behavioral Improvements

* Fixed rendering of fatal errors so that it uses the proper stylesheets.

## Bug Fixes

* Fixed bug where activating a theme only changed the home page.
* Fixed error where all pages added to a multilingual site were showing as system pages.
* Fixed bug where attributes in the application/attributes directory couldn’t be installed.
* Bug fixes with attribute validation.
* Fixed error exception when creating a new page type failed validation
* Fixed bug where Express Forms could not be added on sites that were upgraded from 5.7.
* File Date modified in file manager now shows the proper date (instead of the date added)
* Fixed bug where attempting to delete Express entries or entities that had values attached to express attribute types would trigger an error.
* Attribute search fields in advanced search dialogs now select their options properly.
* Fix misnamed config value concrete.file\_manager.images.use\_exif\_data\_to\_rotate\_images (was named concrete.file\_manager.images.use\_exim\_data\_to\_rotate\_images)
* Fix bug with Legacy Form not being able to be saved under certain conditions.
* Fixed: Entering a new Express Data Object with the existing Handle will cause error

# 8.0.2

## New Features

* Added the ability to use the express attribute to specify express entries in the Express Entry Detail block.

## Bug Fixes

* Fixed site name not rendering in many themes (those that used Config::get(‘concrete.site’) to retrieve it.)
* Fixed inability to set a site to private or members only.
* Fixed error message complaining about methods in missing in the ExpressSetManager interface that made it impossible to work with Express objects in the Dashboard.
* Fixed error that kept sites with legacy attribute categories (like Vivid Store) from upgrading properly.
* Fixed Page Attribute Display block not having access to delimiter field after upgrade from 5.7.
* Fixed ability to save file search queries in site updated from 5.7.
* Fixed https://www.concrete5.org/developers/bugs/8-0-1/conversation-block-attachments-can-not-be-disabled/
* Fixed https://www.concrete5.org/developers/bugs/8-0-1/file-attributes-with-no-file-selected-cause-errors-after-upgradi/

# 8.0.1

## Bug Fixes

* Fixed bug where files were not viewable by anyone other than admin after upgrade from 5.7.5.10.
* Fixed bug where select attribute wouldn’t sort by popularity (and would die with a SQL error.)
* Fixed bug where tracking code was not preserved after upgrade from 5.7.5.10.
* Fixed bug where users could not be deleted after upgrade from 5.7.5.10
* Debug is no longer the default setting for error reporting.
* Fixed inability to sort attribute sets, bugs with editing legacy attribute sets.
* Fixed problems with saving legacy attributes.
* Made file manager behave better in cases where a file record somehow had no versions.
* Fixed error where adding a form block would fail intermittently
* Fixed typos in the automatically generated Nginx configuration for pretty URL handling (thanks chemett)

# 8.0

## New Features


* Express: Extensible, Custom Data Objects that can be created by Editors. Easily search, sort, manage permissions on and display these objects in the front-end and the Dashboard.
* User Desktops: a fully customizable landing page for users when they login to the system, available even if user profiles are not. Functions within the Dashboard or outside of it. 
* Revamped Waiting for Me: can include a large number of notification types (like user signup, workflow, form submissions, private messages, concrete5 updates and more) and is extendable by third parties.


## Block Improvements


* Completely overhauled Form block: now powered by Express, form block fields are attribute-based. This means they can be added to with new attributes. Additionally, you can intersperse text with form controls. The Form block creates Express entities in the Dashboard, which you can grant permissions to, related to other entities, and more.
* More control over page defaults – ability to choose whether to delete all blocks based on defaults or just the unforked versions, and the ability to publish updates to page defaults over previously forked versions of defaults blocks.
* Added the ability to add a delimiter to multiple items displayed by the Page Attribute Display block (thanks cryophallion)
* Add topic, tag, and date filtering to the Page Title block (thanks MrKarlDilkington)
* Add an option to list pages at the current level in Page List (thanks juhotalus)
* Fix image slider composer view (thanks ob7)


## Page Improvements


* Page versions can now be scheduled for approval in the future.


## File Improvements


* Revamped file manager, with support for folders, better support for saved searches, and more.
* Automatically generated thumbnails now work with storage locations (thanks Mnkras)
* New attractive file type icons that better match concrete5’s current UI (thanks Freepik – http://www.flaticon.com/authors/freepik)
* SVG files now will create thumbnails when uploaded if the system has ImageMagick installed (thanks mlocati)


## Stack Improvements


* Stack Folders: Stacks now support folders, which should enable developers to use stacks more efficiently. 


## Dashboard Improvements


* Dashboard Favorites are now Chooseable via the Bookmark Icon in the Dashboard Header


## User Improvements


* User approval is now handled through the use of concrete5 workflow. Enable workflows on user activation to control how users register for your concrete5 site. Control which administrators can edit which users. (thanks Mainio!)
* All user passwords can be globally reset from the Dashboard. Users will have to reauthenticate immediately, and change their password immediately.


## SEO Improvements


* There are now separate tracking codes for header and footer locations (thanks MrKarlDillkington, mlocati)


## Multilingual 


* Multilingual stacks and global areas work nicely with folders.
* Drafts now use the target page location property to determine their locale and language, allowing you to create related drafts for different languages.
* Multilingual sites now appear as their own trees in a tabbed sitemap, rather than within the main site.


## Permissions/Workflow Improvements


* Waiting for Me Workflow List now shows all workflow types instead of just Pages, is fully extendable, more attractive, and available outside of the Dashboard via  Desktop Block.

## Attribute Updates


* Added Telephone, URL and Email Address attributes
* Image/File attribute now has an “HTML Input” display mode.
* Text attributes now have a placeholder as an option (thanks avdevs)
* Custom attributes can now be globally applied to your site, and easily accessed By Calling \Site::getSite()->getAttribute(‘attribute_handle’);




## Other Improvements


* Updated installation process; more attractive, gives users something to do while installation is ocurring, added the ability to specify canonical URL and session handler during installation (thanks mlocati)
* If a site is running on an updated core, the database migrations will automatically be run (saves potential database until the update has to be run manually)
* The command line installer now features an interactive mode when used with -i
* Better checking of .htaccess status when updating pretty URLs (thanks mlocati)
* You can now add page redirects for the home page (thanks edtrist)
* Code cleanup and optimization (thanks a3020, mlocati, Korvinszanto)
* Invalidate browser cache when CSS files are edited (thanks joostrijneveld)
* Switch Site name and page title on default (thanks katzueno)
* We added ID back to the custom style panel for blocks (thanks MrKarlDilkington)
* Improvements to composer autosave behavior.
* We now use relative URLs when the canonical URL isn’t set.
* Nicer display of image slider in edit mode (thanks Siton-Design)
* Fixed linking to twitter tweets so they don’t redirect (thanks clarkwinkelmann)


## Bug Fixes


* Big thanks to olsgreen for fixing a long standing bug with page edit mode checking and timestamps, leading to a fix of buggy edit mode behaviors like layouts not rendering post add, edit mode not being respected, etc... 
* Bug fixes to Image Slider (thanks MrKarlDilkington)
* https://www.concrete5.org/developers/bugs/5-7-5-8/file-manager-edit-image-doesnt-work-when-jscss-cache-is-on-becau/ (thanks mlocati)
* Fixed bug where custom styles in stacks weren’t showing up if the stack was added to the front-end (thanks olsgreen)
* Added  CSRF Tokens to Legacy Form Block (thanks ryantyler)
* Tiny issue: Add missing "/" in $title end tag (thanks Siton-Design)
* Fix issue to generate thumbnail of vertical long image (thanks hissy)
* Fix: loop Setting not working in youtube block (thanks jordif)
* Fix: Switching from a theme with grid support to one without grid support errors out (thanks olsgreen)
* Bug fixes with thumbnail creation logic when the width of the image exactly matches the width of the thumbnail (thanks Mesuva)


## Developer Updates


* Symfony components updated to version 3.
* Font Awesome icon set updated to version 4.5.
* Search block URLs support URL Resolver so they can be overridden (thanks ahukkanen)
* Completely new translation subsystem, with better support for language contexts, and an improved API (thanks ahukkanen and mlocati)
* Bootstrap components updated to 3.3.7.
* Updated Laravel Dependency Injection Component to version 5.
* Zend Framework libraries updated to their latest versions
* Added on_form_submission event for Legacy form (thanks Jozzeh) 
* Additional commands added to command line tool (thanks mlocati)
* jQuery UI updated to 1.11.4


### Important Backward Compatibility Notes


* When deleting database tables in v8, you may have some trouble. This is due to foreign key constraints. See: https://github.com/concrete5/concrete5/issues/3797


https://github.com/concrete5/concrete5/issues/3299


## Credits


In addition to the credits above, the following users have been very helpful fixing bugs, testing beta releases, and helping whip the 8.0 interface into shape


Edtrist, mlocati, MrKarlDilkington

# 5.7.5.13 Release Notes

## Bug Fixes

* Once again, Environment Information is now available in the Dashboard.

## Developer Updates

* Added jQuery Select to Dropdown menu support in the Dashboard; just add data-select=”bootstrap” to your select menus.

# 5.7.5.12

## Bug Fixes

* Fixed bug with Environment Information not working on PHP below 5.4.

# 5.7.5.11

## Bug Fixes

* Works again properly on PHP 5.3.
* Fixed bug that made upgrading impossible on PHP < 5.5.9.
* Fixed page not found error when clicking on a topic list to filter the page list in the blog.
* Controller bug fixes and security updates.

# 5.7.5.7

## New Features

* Nice column view for thumbnail image browsing (Thanks MrKarlDilkington)
* Added Max Width as an option to the Image Slider block (thanks cryophallion)
* Added configuration option concrete.misc.require\_version\_comments (defaulted off) to enable the requiring of version comments (thanks mlocati)

## Behavioral Improvements

* Improved performance and API for parallax scrolling
* Better support for rich text editor and file manager permissions when the user using the rich text editor and the file manager isn’t an administrator.
* Custom styles that are set on composer control output blocks will now be inherited when those blocks are published to a page. (thanks olsgreen)
* Added support for site names in a multilingual site (thanks mlocati)
* Site localization strings are now loaded after core and package localization strings (thanks mlocati)
* Added ability to set override meta keywords from a particular page (thanks katz)
* Facebook authentication uses curl verify peer setting (thanks jaromirdalecky)
* Allow filter select attribute using NOT LIKE through comparison (thanks Ruudt)
* Code cleanup (thanks mlocati, a3020)
* Image slider CSS fixes (thanks robkovacs)
* Use correct target in page list links (thanks ojalehto)
* Add “Required” label to required composer form controls (thanks MrKarlDilkington)
* Prevent empty span from displaying if no title is entered in Page Attribute Display block (thanks Mr
* If an AJAX error occurs during page composer editing, auto-save is now disabled (thanks hissy)
* Cosmetic improvements to marketplace item listings
* Composer custom templates now can be included in packages.
* Preserve original URL when login is needed (thanks mlocati)
* Developers can now add pages under the dashboard that aren’t single pages (thanks herent)
* “Disable Scroll Wheel” option on Google Maps block works on mobile now (thanks hissy)
* Translation tool improvements
* Added DOM Extension to official installation requirements (thanks ChrisHougard)
* Swiss Provinces included in Location List (thanks appliculture/mlocati)
* Location Lists are now translatable (thanks mlocati)
* AutoNav performance improvements (thanks littleibex)
* https://www.concrete5.org/community/forums/5-7-discussion/feature-request-add-filename-colum-option-to-file-manager/ 

## Bug Fixes

* Fixed bug where full page caching would rebuild a page every time it was viewed, instead of viewing from cache.
* Fixed issue where Upgrade Doesn't Complete Fully When Upgrading from a Previous Upgrade (thanks mlocati)
* Fixed hanging that could occur on login when attaching specific users to advanced permissions
* Fixed bug where the table “BasicWorkflowProgressData” could not be inserted into when publishing page edits
* Fixed HTML block clears saved entities on edit (thank acliss19xx)
* Bug fix: multiple workflow on same page causes errors (thanks hissy)
* Avoid InvalidArgumentException with Page Attribute Display block when showing images with both width and height set to zero (thanks hissy)
* Fixed bug with displaying rating attribute values as stars.
* Fixes Zend Queue bug (Empty Trash, etc…) in PHP 7.
* Fixed  https://www.concrete5.org/developers/bugs/5-7-5-6/bootstrap-styles-not-properly-scoped-within-.ccm-ui/#812586 (thanks allybee)
* Fix custom styling with additional file storage location types (thanks hissy)
* Fixed http://www.concrete5.org/developers/bugs/5-7-5-6/userlist-filter-by-group/
* Updated JShrink to fix an issue where minified/compiled JavaScripts used by the asset system would break if comments were included after JS code (thanks 1stthomas)
* Fixed bug where blocks in global areas couldn’t be reordered on the front-end (thanks ojalehto)
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-6/magnific-popup-ipad-bug-fixed-in-latest-version/ (thanks MrKarlDilkington)
* Fixed https://www.concrete5.org/community/forums/usage/squashed-images-mobile-view/ (thanks MrKarlDilkington)
* Fixed: Stack content isn't indexed in the search index (thanks ottovirtanen)
* Fix file url in form results when using a non-public file storage location (thanks ottovirtanen)
* Fixed http://www.concrete5.org/developers/bugs/5-7-5-6/choose-user-not-working/ (thanks mlocati)
* Fixed image slider in theme listings in the marketplace Dashboard
* Fixed https://github.com/concrete5/concrete5/pull/3702 (thanks mlocati)
* Avoid sitemap.xml error on Search Console (thanks hissy)
* Fixed html entities not being preserved in content block (thanks acliss19xx)
* Fix some untranslated messages (thanks hissy)
* Fixed issue where Topic List block returns User Groups
* Fixed inability to create a page named “0” (thanks hissy)
* Fix translated placeholders on storage location paths (thanks ojalehto)
* Fixed issue with thumbnails in the file manager looking too large.
* fixed misnamed gc\_maxlifetime session cookie option making it impossible to configure this value in custom configurations (thanks simoneast)
Bugfix: RSS feeds get cached indefinitely (thanks simoneast)
* Fixed extra UL tags and invalid placement in topic list block.
* Fixed: page\_list block produces invalid HTML5 for RSS link (thanks derykmarl)
* Fixing the wrong link in dashboard/blocks/types to marketetplace listing page (thanks katzueno)
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-6/javascriptlocalizedasset-loads-asset-with-base_url-resulting-in-/ (thanks mlocati)
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-6/setup-of-security.trustedproxies.ips-done-too-late-in-concretebo/ (thanks hissy)
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-6/error-on-conversation-with-deleted-users/ (thanks mlocati)
* https://github.com/concrete5/concrete5/pull/3701 (thanks katzueno)
* Fixed: feature block wasn't pulling paragraph correctly in editmode (Thanks jaredfolkins)
* Fixed Error when accessing "Manage Presets" php7 (thanks mlocati)
* Fixed Display error messages on Concrete password change (thanks Ruud-Zuiderlicht)
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-6/applying-border-radius-requires-non-zero-border-width/#814380 (thanks mlocati)
* Fix: unable to redirect to home on submit form block (thanks hissy)
* Fixed padding and display of toolbar (it was off by a pixel) (thanks zanedev)
* Fixed https://github.com/concrete5/concrete5/pull/3673 (thanks jaromirdalecky)
* Bug fixes to Download Report when users have been deleted (Thanks hissy)

## Developer Updates

* Updated Magnific Popup to 1.1.0
* Improvements to the command line tools (thanks mlocati)
* Added c5:exec CLI command (thanks mlocati)
* update Picturefill to current version 3.0.2 (stable) (thanks MrKarlDilkington)
* add config value to set file manager results per page (thanks MrKarlDilkington)
* Added File Zip Service (thanks mlocati)
* Allow the passing of Page Template handles for Page Type adding/updating (thanks cryophallion)

## Backward Compatibility Notes

* Updating Magnific Popup 1.1.0 drops support for Magnific Popup in IE7.

# 5.7.5.6

## Behavioral Improvements

* Minor improvements to command line utilities (thanks mlocati)
* Default behavior on certain javascript links prevented (thanks ojalehto)
* Fixed: User's avatar's url doesn't change when you change the image (thanks ojalehto)
* Fixed: https://github.com/concrete5/concrete5/pull/3420 (thanks ojalehto)
* Remove New Page link from stacks version history (thanks ojalehto)
* Adjust clear log button to indicate dangerousness of the action (thanks ojalehto)

## Bug Fixes

* Fixed inability to publicly register new accounts (received invalid email address errors on valid email addresses.) (thanks JeRoNZ)
* Fixed http://www.concrete5.org/developers/bugs/5-7-5-5/file-manager-broken-after-deleting-a-file-set./ (thanks ojalehto)
* Parallax custom template causes layout design to not be accessible
* Fixed bug in next/previous block where exclude system pages was always set to true (thanks ojalehto)
* Prevent error while adding a new feed without a page type filter (thanks ojalehto)
* Fix incorrect action after renaming a stack (thanks ojalehto)
* PHP7 bug fixes (thanks JeRoNZ)
* Fixed multilingual flag layout(thanks ojalehto)
* Strict error bug fixes (thanks mlocati)

# 5.7.5.5

## Behavioral Improvements

* You can no longer deactivate or delete your own user account in the Dashboard
* Social Links block opens links in new tabs (thanks MrKarlDilkington)

## Bug Fixes

* Fixed inability to clear site contents when installing themes that swap the site’s contents with their own.
* Responsive flag images in multilingual sites (thanks seebaermichi)
* Fixed issue where pasted blocks weren’t using proper grid container settings.
* Fixed inability to bulk delete files.
* Fixed Form block's questions are ordered incorrectly after ordering some of them and creating a new question. (thanks ojalehto)
* Fixed: An error was thrown e.g. when trying to change user's password in dashboard while MYSQL is used in STRICT_TRANS_TABLES mode (thanks ojalehto)
* Fixed error when adding files to sets and not logged in as admin.
* Fixed inability to login with Oauth-based authentication types, including concrete5.org community and others (thanks Fabian Vogler)
* Fixed bug: Layout column widths are no longer editable after being saved the first time
* Fixed http://www.concrete5.org/developers/bugs/5-7-5-4/member-avatar/
* Minor fixes to certain command line commands (thanks mlocati)
* Fixed https://github.com/concrete5/concrete5/pull/3363 (thanks ojalehto)
* Fixed https://github.com/concrete5/concrete5/issues/2959 (thanks seebaermichi)
* Fixed https://github.com/concrete5/concrete5/pull/3368 (jaromirdalecky)
* Fixed https://github.com/concrete5/concrete5/issues/3365 (thanks Ruudt)

# 5.7.5.4

## Feature Updates

* Lots of improvements to the YouTube block, including responsive and widescreen improvements, support for playlist URLs, support for more YouTube options, and code cleanup (thanks Mesuva!)
* Added the ability to start composer page location sitemaps at a certain level in the tree.
* Share this Page block now includes a print option (thanks ojalehto)
* New uploading settings Dashboard page allows administrators to specify a maximum width, height and JPEG level for images uploaded to the file manager. Images will be constrained using client side JavaScript (if available) and server side as a fallback (thanks Mesuva)
* Background size and position added to options in Background Image section of area/block design (thanks MrKarlDilkington)
* Added the ability to set storage locations for files in bulk (thanks hissy)
* Updates to Image Slider block: draggable and collapsible slides, choose whether to animate automatically, slider speed, time between transitions, and whether to pause on hover (thanks MrKarlDilkington)
* Character count added to bulk SEO updater and SEO panel (thanks Mesuva)
* Added “Fit Image” button to Image Editor (thanks MrKarlDilkington)

## Behavioral Improvements

* If a user has the ability to approve the workflow on a page that he or she is updating, the workflow will be skipped when submission occurs.
* Better validation of thumbnail types created through the dashboard (thanks mnakalay)
* Security improvement: immediate invalidation of password reset emails upon changed passwords (thanks joemeyer)
* We now use the number form element in the number attribute (thanks Remo)
* Added version comment to workflow email.
* Better caching of Page List blocks (thanks TimDix)
* CSS scope fixes and cleanup (thanks robkovacs)
* Drafts now include the date they were created (thanks MrKarlDilkington)
* Command line utilities will now work with a symlinked core (thanks mlocati)
* An area name is now visible when dragging a block over it
* Better compressed image slider sample images lead to smaller file sizes (thanks MrKarlDilkington)
* Improvement to the Page Defaults editing experience (thanks MrKarlDilkington)
* Added support for system pages to the AutoNav block (thanks joostrijneveld)
* Better support for <picture> elements in content blocks (thanks EC-Joe)
* Configuration option added to disable download statistics tracking (thanks EC-Joe)

## Bug Fixes

* Custom theme layout presets now honor attributes on containers and columns other than just “class” (data attributes, etc…)
* Fixed error on user password validation on PHP 5.3.3.
* User avatar removal now protected against CSRF attacks.
* Allows the use of custom label text for file selectors (thanks mnakalay)
* Miscellaneous code cleanup and minor bug fixes (thanks joemeyer)
* Fixed infinite redirect issues with certain setups.
* Fixed https://github.com/concrete5/concrete5/issues/3063 (thanks joemeyer)
* Fixed errors when including job sets in packages (thanks joemeyer)
* Fixed bug where uploading files with uppercase extensions would fail in certain situations.
* Fixed bug where image slider block entries with links to internal page would lose those links on edit (thanks acliss19xx)
* Fixed https://github.com/concrete5/concrete5/issues/3300
* Fix newsflow url to Dashboard's update page (thanks concrete5 Japan)
* Fixed: It is not possible to set the color picker to complete transparency in the theme customization options (thanks mlocati)
* Fixed: if you add a picture to a feature paragraph area (or other abstracted string) and go to edit it it doesn't get translated back (thanks joemeyer)
* Fixed: https://github.com/concrete5/concrete5/pull/3214 (thanks frosso)
* Fixed inability to clear background images in page design.
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-3/remove-alias-does-not-work/
* Bug fixes with Dashboard sitemap and page search.
* Fixed: Package description isn't translated before installing the package (thanks mlocati)
* Fixed: Can't vote in a survey if the block caching is turned on (thanks TimDix)
* Fixed https://www.concrete5.org/community/forums/chat/date-navigation-timezone-problem/ (thanks mlocati and WillemAnchor)
* Fixed https://github.com/concrete5/concrete5/issues/3098 (thanks ahukkanen)
* Fixed bug where the Add new page dialog was missing certain translations loaded from Composer (thanks ahukkanen)
* Fixed https://www.concrete5.org/developers/bugs/5-7-5-3/zip-file-download/ (thanks mlocati)
* Fixed bug where filtering by select attribute option values wasn’t working when the options had special characters in them (thanks dsgraham)
* Added X-Frame-Options header option for security purposes (thanks hissy)
* Fixed https://hackerone.com/reports/4934 (thanks joemeyer)
* Fixed mobile theme switcher issues: Elements are loaded from default theme instead of mobile theme, Responsive image settings of mobile theme does not respected (thanks hissy)
* Content import now properly imports area background images (thanks myconcretelab)
* https://github.com/concrete5/concrete5/pull/3106 (thanks mlocati)
* Fixed typo in Password Sent email template (thanks allybee)

## Developer Updates

* Code improvements to facilitate concrete5 running on PHP 7 (thanks mlocati)
* New command line installation functionality to support installs in a clustered environment (attaches to existing databases rather than requiring an empty database.)
* New command line utilities for installing and uninstalling packages are now available (thanks mlocati)
* New command line utilities for generating and updating package translation files (thanks mlocati)
* Feature: Add new Conversation Message event (thanks brucewyne)
* Page Theme classes can now provide custom value lists. For information on why you’d want to do this, see this issue: https://github.com/concrete5/concrete5/pull/3031
* New attach mode in command line installer: When the --attach flag is supplied with a concrete5 c5:install call, if the supplied database already has rows we will attach to it rather than failing
* Session API Improvements
* Groups tree Javascript now supports multiple selection (thanks Shotster)
* Package controllers can now define on\_after\_packages\_start() methods which will run after on\_start() from ALL installed packages have run. This can be helpful when a particular package requires something from another package, but the original package is executing on\_start() before the dependency.
* Tourist tours now have access to showStep method (thanks danielgasser)

# 5.7.5.3

## Behavioral Improvements

* Added an “Add Content” guide that goes through the process of adding content to the page, and explains the Add Content panel.
* Improved contrast in the Add Content and Dashboard panels.
* Fixed https://github.com/concrete5/concrete5/issues/2980
* Improvements to image editing experience when using the concrete5 image editor.
* Account private messages no longer assumes profiles are enabled (thanks ounziw)
* Escaped input in form submissions so prevent Excel macros from being embedded in fields (thanks TimDix)
* Links in image slider description will automatically substitute the proper URLs even when changing servers (thanks hissy)
* Added logout link to mobile menu (thanks ojalehto)
* Device visibility classes (hide on desktop, hide on laptop,, etc…) are now disabled when a page is in edit mode.
* Additional page URLs preserve query strings on redirecting to canonical URLs.
* Imported area layouts now support custom styles (thanks myconcretelab)
* Parallax custom template on area design now works with multiple parallax areas on a page (thanks myconcretelab)

## Bug Fixes

* Fixed infinite redirect loop with Internationalized Domain Names (thanks EC-Joe)
* Fixed bug where multilingual global areas would sometimes duplicate themselves needlessly, leading to empty global areas
* Fixed hard-to-reproduce duplicate key error in ConversationFeatureDetailAssignments table when using the conversation block throughout your site
* Fixed out of memory errors when uploading large files from the incoming directory (thanks EC-Joe)
* Fixed “When using inline blocks, I can edit other inline blocks” (thanks TimDix)
* Fixed errors with blocks that have assets not having their assets included if those blocks were within a layout. Fixed error with google maps block specifically.
* Fixed error with scrollbar not appearing after file uploaded on the front-end (actually fixed this time.)
* Fixed Adding and Moving a Block in One Step Causes JS Error
* Resolved: Rich text editor adds in random "=" symbols sometimes
* Resolved: Rich text editor wraps selection in undefined when choosing a custom style
* Fixed but where Downloading a file that exceeds the available memory today causes an out of memory issue
* Fixed occasionally bug that resulted in error “"Argument 1 passed to Concrete\Core\Permission\Access\Access::create() must be an instance of PermissionKey, Concrete\Core\Permission\Key\AdminKey given."
* Fixed bug when moving blocks in certain situations (thanks Remo)
* Fixed: Topics attributes marked as required on pages weren’t being properly validated.
* Fixed some minor XSS potential issues with social links (thanks EC-Chris)
* Fixed bug: Internal Links in Feature Blocks Store Absolute URL in Database
* Fixed: config value “concrete.updates.auto\_update\_packages” now works again
* Fixed fatal error when enabling package auto updates (thanks EC-Joe)
* Fixed error autoloading packages when working with the command line (thanks EC-Joe)
* Approve changes now shows up when moving blocks in stacks (thanks WillemAnchor)
* Fixed bug where editing permissions in simple permissions mode wouldn’t apply multilingual settings administration to the appropriate groups (Thanks Remo)
* Fixed possible CSRF security issue in Conversations settings dashboard page.
* Fixed free-form layouts that on occasion would break into two rows as widths wouldn’t match properly (thanks wstoettinger)
* Color picker JavaScript now properly escaped so it can be used with PHP array syntax.
* Fixed: If you added a BlockTypeSet but didn't add anything to them it would cause the foreach to error on a null value (thanks joe-meyer)
* Fixed inability to filter lists by multiple select values (thanks markbennett)
* Fixed http://www.concrete5.org/developers/bugs/5-7-5-2/date-attributes-search-method-doesnt-work/ (thanks haeflimi)
* IP Blacklist no longer bans on failed registrations (thanks joemeyer)
* Fixed https://github.com/concrete5/concrete5/issues/3048 (thanks joemeyer)

## Developer Updates

* We now default to the “GD” image processing library for image manipulation. Imagick must be opted into by  setting the config value “concrete.file_manager.images.manipulation_library” to “imagick”.
* Adds ability to specify wildcard page theme classes by creating an array key with “*” as its key (thanks TimDix)
* Database Entities dashboard page now refreshes package-specific entities as well as
application-specific entities.
* Implemented new Validation framework and some useful constraints. Used within password validation.
* API improvements to the Processor class to allow it to be used without a queue.
* Select attribute option API improvements
* Edge case page list sorting fix when adding to the query with addSelect and attempting to sort by the new field, and use pagination as well.

## Backward Compatibility Notes


* If you were relying on Imagick image manipulation, you will now be using GD image manipulation unless you manually set “concrete.file_manager.images.manipulation_library” to “imagick” within a custom config file.

# 5.7.5.2

## Feature Updates

* You can now filter the Page List block by date, including pages with a public date of today, X days in the past, X days in the future, and a custom date range (thanks TimDix)
* The File block is now available in the Composer view for a Page type (thanks TimDix)
* You can now export the Database Query Log to CSV (thanks TimDix)
* The Cache settings page now gives developers the ability to optionally create CSS source maps from compiled LESS files.
* Version list now shows who approved the version (thanks Katz)
* Added page template to advanced page search.
*  New modes for page composer where you can choose target pages from an in-panel sitemap, rather than the popup selector.
* Select custom attribute now uses the Select2 JavaScript library for tagging modes, leading to an improved appearance and nicer code behind the scenes.

## Behavioral Improvements

* Improved appearance and information display of controls on the composer form page type dashboard page (thanks TimDix)
* Blocks added to the scrapbook will now honor the original block’s cache settings (thanks TimDix)
* Area layouts will now be cached if all the blocks they contain are cached (thanks TimDix)
Adds ability to cache Search Block if the block doesn't display results - useful for when placed in header/footer (thanks TimDix)
* Performance improvements in the Assets Subsystem (thanks joe-meyer)
* We now include the “position” property in the search index when using the testimonial block (thanks hissy)
* Better performance when working with bulk files and file sets with a large number of file sets (thanks TimDix and jefharris23)
* Stack blocks now check to see if the blocks within the stack can be cached – if so, they will be cached as well (thanks TimDix)
* Resolved https://github.com/concrete5/concrete5/pull/2911 (thanks Shotster)
* Added error messaging when adding or editing page types and not configuring the publishing settings properly.
* Better error reporting when http:// or https:// omitted from canonical URLs (thanks mnakalay)
* Removed “Meta Keywords” from SEO panel on new installs because it’s not actually something that most search engines like anymore (thanks Mesuva). The attribute is still available and installed.

## Bug Fixes
* Fixed bug where layouts with custom widths didn’t honor those widths (thanks kaktuspalme)
* Fixed bug where area layouts disappear upon changing layout design changes (thanks TimDix)
* Fixed issue installing on PHP 5.3.9 and earlier (5.7.5.1 was supposed to fix this but did not.)
* When deleting files, some rows were left in child database tables. This has been fixed (thanks EC-Joe)
* Block actions in edit mode (introduced in 5.7.5) now work with blocks in Composer.
* Permission access entity types can now be provided in packages like they could in 5.6.
* Permission keys can now be provided in packages like they could in 5.6.
* Rich text editor toolbar was abnormally large when present in the attributes dialog window. This has been fixed.
* Fixed bug where Image block fails on Elemental when using certain third party file storage location types with no thumbnail types installed (thanks Mnkras)
* We now show a confirmation dialog when discarding page drafts (thanks hissy)
* Fixed bulk SEO Updater not updating the home page.
* Fixed editor tooltips and link edit callouts not displaying when using redactor in a dialog.
* When setting sitewide permissions in simple permissions mode, “Edit Page Type” hadn’t been set. It also wasn’t set by default when installing concrete5. This is fixed.
* Fixes Bug with Search Block when resultsURL specified instead of page (thanks TimDix)
* Fixed https://github.com/concrete5/concrete5/pull/2894 (thanks skybluesofa)
* Fixed https://github.com/concrete5/concrete5/issues/2362 (thanks TimDix)
* Fixed Fix Cancel button action on block aliasing dialog (thanks hissy)
* Fixed scrollbar not appearing after file upload (thanks EC-Chris)
* Fixed exception when passing an non-number to ccm\_paging\_p (thanks SkyBlueSofa)

## Developer Updates

* Added custom file import processes for forcing JPEGs, forcing JPEG compression and forcing width/height. Added system for creating custom file import processes and calling them programmatically
* Added the ability to try and use exif rotation data (experimental, toggle on by enabling with the config value concrete.file_manager.images.use_exif_rotation_data)
* Translation improvements (thanks mlocati)
* Added flash message support to page controller. Just call $this->flash(‘key’, ‘value’) and then a page redirect and the $key will be available from within the target page the same as if it had been set from that target page. (e.g. $this->flash(‘success’, ‘Thanks for your submission!’); $this->redirect(‘/to/new/page’); )
* PageSelector::quickSelect now works again.
* Page Type Validator framework improvements
* Slight fixes to form labels in form block (thanks haeflimi)
* Improvements to permissions content import XML functionality.
* Fix potential data loss when working with packages that had both db.xml files and Doctrine entities (thanks Mainio)
* Content block image placeholders now save all attributes placed on the images in the rich text editor (Thanks TimDix)
* Fixed permissions error rendering “subscribe to conversation” functionality inoperable.
* Improvements for working with PHP7 (thanks mlocati and Mnkras)
* Added additional MIME extensions for new Office file types (thanks RGeelen)
* on\_page\_get\_icon event now works properly (thanks ahukkanen)
* Lots of code quality improvements (thanks joe-meyer and mlocati)
* Fixed https://github.com/concrete5/concrete5/issues/2952 (thanks ahukkanen)
* New console command available: Clear Cache (thanks mlocati)

## Developer Backward Compatibility Notes

* The signature of the \Concrete\Core\Page\Type\Validator\ValidatorInterface has changed. If you rely on this interface check your implementations. (Note: if you extend the \Concrete\Core\Page\Type\Validator\StandardValidator you should be fine.)

# 5.7.5.1

## Behavioral Improvements

* Better checking for InnoDB database tables than querying INFORMATION_SCHEMA directly.
* Improved accuracy and performance of the parallax scroll area layout custom template.
* Fixed Fatal error when getPageThemeGridFrameworkRowStartHTML() and getPageThemeGridFrameworkRowEndHTML() return nothing

## Bug Fixes

* IP Blacklist functionality now works correctly
* Fixed non-functioning image editor when editing image thumbnails.
* Fixed error “PHP Fatal error: Can't inherit abstract function” on PHP 5.3.9 and earlier
* Fixed errors installing and working with concrete5 on MySQL setups with strict tables enabled.
* Fixing tree topic error in flat filter custom template when you have removed the topic tree its linked to
* Fixed misnamed header grid classes in Elemental theme (thanks hdk0016)
* Fixed http://www.concrete5.org/developers/bugs/5-7-4-2/date-type-custom-attributes-was-not-add-default-block/
* Added legacy Image helper class (\Concrete\Core\Legacy\ImageHelper) back. This class had been moved to BasicThumbnailer and was working for all proper usage of the class, but for those instances where the class was hard-coded a the legacy image helper, the class is back for the time being. **It will be removed in a subsequent update.**

# 5.7.5

## Grid and Layout Improvements

* Page Theme classes can specify layout presets, which can use classes contained in grid frameworks or use their own custom classes.
* Layouts now have design controls available to them, including custom templates and custom CSS classes.
* Added a new custom template “Parallax Image” available to layouts that employ a background image.
* Grid frameworks can now specify hiding classes for responsive breakpoints, which can be controlled through block and area design settings.
* Grid containers that wrap around blocks based on their type can now be disabled or enabled on a per-block basis through the block design palette.
* Added nested support to grid frameworks.

## Mobile Improvements

* Completely new Mobile Device Preview panel in the page panel. Preview the current page in a variety of mobile form factors, simulating user agent, and even rotating the device.

## Multilingual Improvements

* Global areas and stacks are now multilingual: if you have multiple language areas in your site, stacks and global areas you add will have separate instances for each language, and the appropriate stack contents will be displayed on the appropriate pages with no hacks.
* You can scan a multilingual section for all links and references to multilingual pages, and if those pages exist outside the current tree, they will be remapped into the current tree. (i.e after you copy a multilingual tree, you can rescan its links so they don’t point to the original tree.)

## Other Feature Updates

* Elemental now provides two layout presets – Left Sidebar and Right Sidebar.
* You can now set an RSS feed to be filtered by a particular topic
* You can now add an image to an RSS feed
* If you register a site that requires approval before logging in, you will receive an email letting you know this is the case (thanks ounziw)
* You can now turn off help via a checkbox in the Dashboard on the Accessibility page.
* The file block now contains an option to force download (thanks Mesuva)
* Next/Previous Block now supports reverse ordering options (thanks UziTech)
* You can now run concrete5 jobs from the command line using concrete/bin/concrete5 c5:job (thanks ChrisHougard!)
* You can now choose the background image for full-image background pages with the  'concrete.white\_label.background_url' config option (thanks myconcretelab)
* Redactor rich text editor has been updated to version 10.2.2,. fixing many bugs and adding some small features.
* Adds support to adjust trusted proxy ips and settings through Config values (thanks timdix)



## Behavioral Improvements

* Login page now much easier to theme. Should look nice in stock Elemental theme. More generic language and hides the authentication type list of only one authentication type is enabled. No more background image when attempting to re-skin login page in another theme.
* File manager import incoming now has a checkbox to select all files (thanks MeyerJL)
* Table cells in rich text editor have a minimum width of 55 pixels (thanks KarlDilkington)
* Group set names can now contain multibyte characters (thanks hissy)
* More rich text editor plugin interfaces are translatable (thanks mlocati)
* Fixed Typography selector fails on save if it is used without font selection (thanks ojahleto)
* Permissions are properly checked when displaying the publish button and the delete button in composer (thanks hissy)
* Editing page defaults no longer prompts you to save or approve your changes, since changes to page defaults are immediately live (they are not versioned.)
* Improved performance of full page caching (thanks EC-Chris)
* Improvements to session handling when the session directory exists outside of an open_basedir restriction (thanks acohin and mlocati)
* Page attributes are now grouped in sets on the page type defaults attributes screen (thanks EC-Joe)
* Form block now highlights errors on specific fields when they aren’t filled in properly (thanks timdix)
* Fixed bug that caused areas to have problems if they were converted in code from GlobalArea to Area and vice versa (thanks joe-meyer)
* Fix: can't override install options by config file (thanks mlocati and hissy)
* Better dialog message when the user can not select files (thanks hissy)
* Display last used authentication type if authentication fails (thanks ChrisHougard)
* Authentication types that rely on mcrypt use a more reliable random number generator (thanks thomwiggers)
* You can now export logs to CSV files from the Dashboard page (thanks timdix)
*  If the package contains a theme that's currently active on the site, the package uninstallation can't occur
* Gravatar user avatars now honor the passed aspect ratio parameter when using a custom aspect ratio (thanks joostrijneveld)
* Fixed https://github.com/concrete5/concrete5/issues/2522

## Bug Fixes

* Fixed broken list element HTML on dashboard pages when no child pages existing in a certain section. (thanks jaromirdalecky)
* Lots of configuration cleanup, removal of unused configuration values (thanks mlocati)
* Fixed bug where a deleted block type could cause problems for scrapbook blocks that referenced blocks of that type (thanks MeyerJL)
* Fix Base table or view not found: MultilingualSections error when installing in a language other than English
* Fixed bug where there could be only one basic workflow assignment (thanks hissy)
* Miscellaneous UI improvements (thanks mitchray)
* Lots of miscellaneous bug fixes to community points and badges
* Removed old unused timezone constants and replaced with proper configuration values (thanks mlocati)
* Fixed bug where Blocks on global areas don't prevent full page caching with the setting "On - If blocks on the particular page allow it (thanks TimDix)
* The global configuration value for JPEG compression wasn’t being accessed properly, was ignored. This is fixed (thanks mlocati)
* Email service had been ignoring the default configured name (thanks mlocati)
* Use \Exception and translate line in BannedWord (thanks mlocati)
* Fixed error when saving a type with underline option unchecked in theme customization (thanks ojahleto)
* Fix If you change an Attributes name, those changes do not take effect on the Composer Edit form. You need to delete the attribute and add it again (thanks EC-Joe)
* Fixing bug in topics where topics of multiple words would all be capitalized
* Configuration options are more reliably displayed when using caches like PHP opcache, APC, etc.. (thanks mlocati)
* External links are properly outputted in page list blocks now (thanks GlennSchmidt)
* Fixed Fixing ipv4 to ipv6 address bugs (thanks MeyerJL)
* Fixed error editing testimonial blocks when the image of the testimonial had been removed from the file manager (thanks edbeeny)
* Fixed error where certain checkbox attributes were being imported as defaulting to checked, when they shouldn’t have been.
* Fixed bug where running \Page::getByID on startup with a page you're currently editing breaks edit mode (thanks EC-Joe)
* Fixed https://www.concrete5.org/community/forums/5-7-discussion/image-slider-links/#752359
* Responsive images served by the picture tag now work in IE9 (thanks mitchray)
* Surveys in global areas are now properly displayed on the survey results dashboard page (thanks EvgeniySpinov)
* Fixed inability to select topics to create under a new topic tree.
* Fixed validation incorrectly claiming a file attribute didn’t exist when checking a page in from edit mode (thanks mitchray)
* Fixed bug with broken URL in testimonial block (thanks KarlDilkington)
* Fixed https://github.com/concrete5/concrete5/issues/2623
* Fixed pagination in form results (thanks mitchray)
* Fixed overrride permissions for user groups not working
* Fixed https://github.com/concrete5/concrete5/issues/2451 (thanks mlocati)
* Style customizer for theme should be easier to use on options that have colors but no fonts available
* Fixed If you create a Checkbox page attribute and select The checkbox will be checked by default. When adding the attribute to pages the box is not checked
* Fixed https://www.concrete5.org/developers/bugs/5-7-4-2/cannot-reset-theme-customization-for-this-page/
* Fixed If you does not have access to group search, you'll get a JSON error message (thanks hissy)
* Fixed filtering by log status levels on Dashboard page
* Fixed http://www.concrete5.org/developers/bugs/5-7-4-2/bug-with-tags-attribute-type1/
* Fixed bug where duplicated pages couldn’t have their block content edited in composer (thanks katzueno)
* Username validation error string fixes (thanks ounziw)
* Fix class not included in legacy page list (thanks hissy)
* Fixed bug: Add layout to area. Without refreshing page, edit container layout of new area, then cancel. Layout looks weird

## Developer Updates

* Big thanks to mlocati for delivering a completely new way to specify database XML, built off of the Doctrine DBAL library, including its types and functionality instead of ADODB’s AXMLS. Database XML now has support for foreign keys, comments and more. Doctrine XML is a composer package and can be used by third party projects as well. More information can be found at https://github.com/concretecms/doctrine-xml.
* $view->action() now works for blocks in add and edit templates. This makes block AJAX routing much easier (simply reference $view->action(‘my\_method’) in your block add/edit template, and implement action\_my\_method) in your block controller.
* Code cleanup and API improvements and better code documentation (thanks mlocati)
* Configuration and old PHP constants removed and replaced (thanks mlocati)
* Completely new approach to command line utilities built off of the Symfony command line class; existing utilities ported (thanks mlocati!)
* Adds ability to add Social Icons via config. (thanks TimDix)
* Packages can also add command line utilities through their on\_start() method (thanks hissy)
* Flag images for multilingual sites can now be specified in application/images/countries/ as well as theme/current\_theme/images/countries (as opposed to coming solely from concrete/images/) (thanks akodde)
* Custom file type inspectors now work again.
* Block types are checked to see if they exist prior to import (thanks Remo)
* Attribute keys are checked to see if they exist prior to import (thanks Remo)
* Permission keys are checked to see if they exist prior to import (thanks Remo)
* Upgraded to Zend Framework 2.2.10 to fix certain internationalization issues (thanks mlocati)
* Fixed duplicate success message on cloned form blocks on the same page (thanks bluefractals)
* Fixed bugs installing concrete5 with strict mysql tables enabled (thanks mlocati)
* Updated Magnific Popup to 1.0 (thanks mitchray)
* If you’re running an OpCache like PHP’s Opcache, APC, XCache or something else, when you clear the cache this cache will also be cleared (thanks mlocati)
* Can compute hash key based on full asset contents if so desired, using the concrete.full\_contents\_asset\_hash config value (thanks mlocati)
* Page cache adapters can now be loaded from places other than the core namespace (thanks hissy)
* updateUserAvatar now fires on\_user\_update event (thanks timdix)
* Attribute sets no longer need to have unique handles across different categories (thanks ijessup)
* Delete page event now can be cancelled by hooking into the event and settings $this->proceed to false (thanks mlocati)
* You can now customize the session save path through configuration (thanks mlocati).
* Updated picturefill.js library to 2.3.1.
* You can now specify your environment for configuration through an environment variable (CONCRETE5_ENV) as well as through host name (thanks ahukkanen)
* File manager JavaScript API improvements

# 5.7.4.2

## Behavioral Improvements

* Saving only a custom template on a block will no longer wrap that block in a custom design DIV. Better saving and resetting of custom designs on blocks and areas.
* Topics improvements: topics can now be created below other topics; the only different between topic categories and topics is that categories cannot be assigned to objects, only topics can.
* We now include the page ID in the attributes dialog and panel.
* Feature block now contains an instance of the rich text editor (thanks MrKarlDilkington)
* Improvements to new update functionality when site can't connect to concrete5.org
* Improvements to new update functionality to make it more resilient with failures, but error messaging.
* Adding attributes to a page will ask for it be checked back/approved when clicking the green icon.
* Theme name and description can now be translated (thanks mlocati)
* Added an error notice when deleting a page type that’s in use in your site.

## Bug Fixes

* Some servers would redirect infinitely when activating a theme or attempting to logout. This has been fixed.
* Fix bug with multiple redactor instances on the same page and in the same composer window causing problems.
* Better rendering of empty areas in Firefox (thanks JeramyNS)
* Fixed problems with “concrete.seo.trailing_slash” set to true leading to an inability to login, other problems.
* Attributes that had already been filled out were being shown as still required in page check-in panel.
* Fixed bug where full URLs were incorrectly parsed if asset caching was enabled (thanks mlocati)
* Fix download file script leading to 404 errors after you go to the dashboard and hit the back button
* Fixed https://www.concrete5.org/developers/bugs/5-7-4-1/dont-allow-to-create-file-sets-with-names-containing-forbidden-c/
* Fix https://www.concrete5.org/developers/bugs/5-7-4-1/cant-replace-a-file-with-one-in-the-incoming-directory/
* Fix XSS in conversation author object; fix author name not showing if a user didn't put in a website (thanks jaromirdalecky)
* Searching files, pages and users by topics now works in the dashboard
* Picture tag now properly inserted by Redactor when working with themes that use responsive images.
* Fixed z-index of message author and status in conversations dashboard page.

## Developer Updates

* API improvements to the RedactorEditor class.

# 5.7.4.1

## Behavioral Improvements

* Add config setting to enable / disable help system (thanks akodde)
* Redirects with trailing URL slashes to non-trailing (or vice versa) now use the 301 code instead of 302.
* Code cleanup and bug fixes to form helper class (thanks mlocati)
* Miscellaneous code cleanup and notice error reduction (thanks mlocati)

## Bug Fixes

* Fixed inability to save blocks, work with dialogs, do many things while asset caching was enabled (thanks mlocati.)
* Fixed certain panels and dialog windows not opening on Windows servers (thanks mlocati)
* Fixed bug when using "S" option to format date (incorrectly displaying as seconds) (thanks mlocati)
* Bug fixes with dashboard get image data URL (thanks mlocati)
* Fixed malformed URL in "Load More" in dashboard sitemap (thanks mlocati)
* Fix unquoted SQL input in permission assignment method (thanks mnkras)

# 5.7.4

## Help System Updates

* Completely new help system, with guided walkthroughs, multiple videos and more.

## Conversations Feature Updates

* Using the Conversation block with non-logged-in users now behaves more like a Guestbook block. It provides a place for a name and email address, and uses the captcha for validation.
* You can now receive notifications when new messages are posted to your conversations. This option is also overridable at the block conversation level. Registered users can also subscribe to conversation updates through an end-user UI.
* Conversation Add Message permission now has the ability to set new permissions by a particular access entity to approved or unapproved by default. (e.g. let guests post but make their posts unapproved by default, while letting registered users post with no restrictions.)
* Conversations Dashboard interface now has filter by deleted, approved, unapproved or flagged message options available.
* Better display of message status in Conversations Dashboard interface.
* You can now sort by message posting date ascending or descending in the Conversations Dashboard interface.
* Conversations Dashboard message list now gives you a contextual menu when clicking on a message. Actions include flagging, unflagging, deleting, undeleting, approving and viewing the original page of the message.
* Non-logged-in posts will use gravatars if that option is checked in the Dashboard.

## Editor Improvements

* Update to Redactor 10, which features an upgraded API for developers and numerous bug fixes.
* New Plugin: Undo & Redo
* New Plugin: Special characters palette (thanks Mesuva!)
* Lightbox can now have its width and height specified for web page links.
* Better handling of URLs loading in lightbox (now loads them in an iframe)
* Can now open links in a new tab.
* Editors can be more easily called programmatically, through the editor service.
* Rich text editor plugins can be added through marketplace add-ons and custom packages.

## Mobile Editing Feature Updates (thanks Hissy!)

* You can now edit a page in composer view on mobile devices.
* Hide mobile menu on checking out a page in edit mode.
* Notification alerts are now responsive.
* Redactor rich text editor is now usable on mobile devices.
* Notification window is mobile friendly.
* Search results in dashboard pages are friendlier on mobile.
* Mobile menu button is active properly in edit mode.

## Other Feature Updates

* Better dashboard update process that checks for compatible add-ons, gives more information about upgrades.
* Uploading files to the file manager now gives you a success dialog in which you can edit the uploaded files’ attributes, assign them to sets, or choose them for an image block, etc...
* Improved site interface translation dashboard page. Can see context, comments, search and translate plurals (thanks mlocati)
* You may now choose multiple files from the file manager if a block or editing interface supports it (thanks olsgreen!)
* You can now add blocks to an area by clicking on the area and selecting “Add Block”. This will open the side panel and you may click a block, stack or clipboard entry there to add to the selected area.
* You can now filter a page list by a specific topic.
* Lots of updates to Multilingual system for better translation extraction, better experience with plurals, bug fixes, other improvements (thanks mlocati)
* Ability to choose a custom canonical URL for the page, instead of always having that canonical URL locked to the URL slugs and absolute site structure.
* You can once again set a custom template for a block at the area level with $area->setCustomTemplate(‘block_handle’, ‘custom_template’); This should be less buggy than it was in 5.6.x as well.
* You can now set a custom template in a page type output page for a composer output control block.
“ ‘More options’, including the ability to import files from remote URLs and the incoming directory is now available from the file manager in front-end page mode.
* Nicer file set administration, including the ability to sort all files in a file set by different criteria for reordering (thanks goutnet at EC-Joe)
* Much faster installation process for Elemental Full. Much lower memory footprint.
* More useful Dashboard Package Details screen (thanks goutnet)
* Archive custom template for the Page Title block now shows the value of the current topic on pages where content is filtered by topic.
* Share this Page block now supports Google Plus and Pinterest
* You can now specify the name of the form submission button (thanks EC-Joe)
* Breadcrumb custom template now available for Auto-Nav (thanks hissy)
* You can now specify what kind of HTML tag you want to use in the Page Title block (thanks dclmedia)
* Maintenance mode now is permission controlled. Those who have the “View site in maintenance mode” permission can edit and access the site even while maintenance mode is turned on (thanks ExchangeCore)
* You can now specify the “canonical host”, “canonical port” and https:// settings of your site in the URLs dashboard page. You can also control whether your site is forced to render at this exact combination (for SEO purposes.)  This setting will also be used by the Domain Mapper and other add-ons.


## Behavioral Improvements

* Clicking on a page attribute now scrolls the page attribute detail panel down to the bottom to make it clear one was added (Thanks mesuva)
* Page title now updates when using the topic list on a blog entry page or elsewhere (thanks hissy)
* Newsflow is now friendlier on mobile, has as nicer appearance, obeys other dialog shortcuts (escape to close)
* Related pages in different languages are now denoted thusly in the sitemap.xml (thanks mlocati)
Instead of defaulting to the current time/date, form block date/datetime have the option of starting empty or defaulting to the current date (thanks MeyerJL)
* You can now search by page type again in the page search interface.
* Minor installation error messaging improvements (thanks Mnkras)
* Some style improvements to panels (thanks hissy)
* File manager now keeps the same file types when creating thumbnails (keeping pngs transparent, etc..) (thanks mitchray!)
* Style improvements to Auto-Nav and Page List block forms.
* We no longer attempt to retrieve packages from the marketplace if you’re not connected, improves performance (thanks goutnet)
* Bug fixes to antispam settings page and system in general (thanks EC-Chris)
* Form block now redirects you to the proper spot on the page for success message (thanks ahukkanen)
* Better detection of changed cached assets (thanks mlocati)
* concrete5 should run better in IE9.
* Files saved through the image editor should much smaller now.
* Better compression of localized assets, better localized asset support (thanks mlocati)
* Non-logged-in users accessing protected pages will be forwarded to those pages upon successful login (thanks deanwhillier)
* Speed improvements to the installation procedure.
* Image thumbnailing should use much less RAM, should work more reliably with larger images.
* Better sorting of block types in the Add Block panel (thanks JohnTheFish)
* When duplicating multilingual page trees, pages that already exist will be skipped (thanks ezannelli)
* Improved reliability and functionality of HTML emails (thanks mlocati)
* Additional page paths now redirect with a 301 header (thanks Mainio)
* Importing page type default attributes now works.
* Better translation of topic trees and topic tree nodes (thanks mlocati)
* Content import with block type sets will now use existing sets if they are available.
* Conversations block now includes its content in the search index (thanks mkly)
* Significantly improved performance of the on-demand file thumbnailing utility when a cached version is found (thanks ijessup)
* Custom block design style fixes – don’t output a style tag when just changing a custom template, better style tag support (thanks mlocati)
* You can now unmap a page in the multilingual page report.
* You can now set the minimum and maximum ranges of style customizer sliders by defining concrete.limits.style_customizer.size_max and concrete.limits.style_customizer.size_min (thanks EC-Joe)
* respond.js and html5-shiv.js are now optionally included by themes, rather than being hard-coded for IE8 and below.
* You can now embed the block controller for this share this page block in a page template more easily.
* You can now specify permissions and attributes for external links (thanks mitchray)
* Better scrolling in add block panel on Firefox (thanks EC-Joe)
* Fixed https://github.com/concrete5/concrete5/issues/875

## Bug Fixes

* Fixed sorting of FAQ Entries in the FAQ block.
* Fixed bug that led to selected topics in topic tree not appearing selected on editing.
* Placing view files in the application/views/ will now work (thanks RuspinaDev)
* Fixed bug with social links block not displaying properly on sites that didn’t already load Font Awesome. (thanks jaromirdalecky)
* Facebook authentication should work again (thanks EC-Joe)
* Fixed bug where If the HTML block is saved without any changes (thus not triggering the on change event), the textarea remains empty and the content is lost (thanks mitchray)
* Fixed inability to have multiple form blocks or survey blocks or blocks with interactive form submissions on the same page and not have submission affect both of them.
* Image slider should work properly in composer.
* Fixed bug in content importer where page types with package attributes weren’t having their packages set properly.
* Choose language on login now functions correctly (thanks mlocati)
* Interactive blocks like form and survey and now be included in stacks and displayed on pages (thanks nicemaker)
* Bug fixes to composer editing experiences where blocks couldn’t be loaded in composer.
* Fix error when searching by approved or unapproved version. Miscellaneous display improvements to search interfaces in the Dashboard.
* The “addAttachment” method in the Mail Service now works again (thanks SnefIT)
* Miscellaneous fixes to content exporter to make it more resilient.
* Fixed bug where “Public Date/Time” core property wasn’t being properly displayed or saved in composer.
* Fixed bug in page attribute display block where complex attribute types couldn’t always be printed out.
* Fixed bug where jobs couldn’t be scheduled to run through browser visit.
* Fixed HTML block tooltip getting cut off (thanks mitchray)
* Remove old page versions job now works again.
* Cookie settings bug fixes (thanks tao-s)
* Fixed MP4 video files not showing up as the right file type in the file manager.
* Bug fixes with multilingual browser detection (thanks ezannelli)
* Fixed bug with packaged page type controllers not being properly used as page controllers.
* Fixed infinite redirect on multilingual websites that set the Home Page as their default language page (thanks mlocati)
* Better behavior with advanced permissions and users who can only view their own files in the file manager.
* Bug fixes to custom external forms.
* Fix bug deleting file version object and then attempting to add new versions might give attribute errors.
* Bug fixes to configuration values in session cookies, database backed sessions (thanks tao-s)
* Better permissions checking in the file manager (thanks hissy)
* Drafts now show up in the sitemap again; tweaks to fix sitemap showing unapproved pages.
* Fixed bug with topic list block not displaying topics for a page properly.
* Topics can now contain ampersands and other special characters.
* Localization bug fixes (thanks mlocati)
* Fixed http://www.concrete5.org/community/forums/customizing\_c5/strange-workflow-error/
* Feature block link option now works with the hover description custom template”
* Fixed programmatic filter by checkbox attribute not displaying all appropriitems if passing “false” to the option.
* Fixed bug where single page controllers in application/ directory weren’t working.
* Better inheritance of area permissions to blocks in areas when inheriting permissions from page types in advanced permissions mode (thanks hissy)
* Fixed for file sets for better sanitizing, miscellaneous usage fixes (thanks Mnkras)
* Fixed broken area styles when using more than one custom class on an area (thanks jordif)
* Bug fixes to color picker widget when used in a block dialog (thanks olliephillips)
* Fixed fatal error that would display in area permissions dialog when attempting to use advanced permissions to inherit permissions from an area set in page defaults (Thanks hissy)
* Fixed potential cross site scripting error in composer detail form.
* Fixed “"Navigate this page in other languages" - Invalid argument supplied for foreach()” that could happen with unmapped multilingual websites.
* Fixed issue where dashboard panel would not stay closed if closing manually.
* Localization fixes to Page Type Composer Control Name (thanks hissy)
* Bug fixes and better sanitizing when saving Banned Words in the Dashboard (thanks Mnkras)
* Better page permissions set on drafts page for users of advanced permissions mode (thanks hissy)
* Bug fixed where Add Survey, Approve Page, Edit Survey, save – survey listed twice in the Dashboard. (thanks ECJoe)
* Fixed http://www.concrete5.org/developers/bugs/5-7-3-1/multiple-versions-of-a-page-cannot-be-deleted-at-once/
* Fixed Unable to edit a user when concrete.seo.trailing\_slash is enabled (thanks ECJoe)
* Workflow progress categories are now uninstalled when uninstalling packages (thanks mkly)
* Fixed bug when removing group or user from “Add SubPage” permissions in advanced permissions mode.
* fixed bug with Reply to this email address (thanks MeyerJL)
* Better display on editing grid layouts when working with layouts that have multiple column classes (thanks ezannelli)
* Fixed malformed Page Cache Expires header when using full page caching.
* Conversations: fixed javascript errors when not using redactor editor.
* Conversations: fixed attachment disabling not removing the attach file button when editing a message.
* Minor page type composer validation bug fixes
* Packaged permission key fixes (thanks mkly)
* Packaged workflow fixes (thanks mkly)
* Fixed appearance of pagination on form results dashboard page.
* Fixed pretty URLs not being invoked for certain block actions, in other situations. Normalized pretty URLs and made them work better.
* We now properly used custom scrapbook view layers for blocks added from the clipboard on the stacks dashboard page.
* Fixed bug where applying timed permissions to a copied page change the permissions object of the original page.
* Fixed XSS sanitization issues in private messages (thanks Mnkras)
* Fixed minor XSS issues (thanks Netsparker)
* Data URL images in CSS files are correctly preserved in asset caching (thanks mlocati)
* Fixed http://www.concrete5.org/developers/bugs/5-7-3-1/moving-blocks-in-a-stack/
* Fixed Replacing file throwing erroneous "file is too large" error message
* Fixed Bulk Editing file properties does not add new File Versions
* Lots of bug fixes to page aliases, including bug where original page would be deleted if an alias was in the trash and the trash was emptied.
* Automated groups on login or register will automatically be entered if a custom automation controller doesn’t exist (thanks Mnkras)
* Fixed http://www.concrete5.org/developers/bugs/5-7-3-1/user-search-shows-same-user-multiple-times/#732257
* Fix display order issue of aliased pages (thanks hissy)
* Fixed Can't create link to file or page from within composer form
* Fixed Page List Filtering By Page Type and Show Aliases
* Fixed bug in exists() method in Cache library (thanks SnefIT)
* Fixed HTML validation error when using built-in Securimage Captcha
* Fixed preview icon in Feature block (thanks zneek)
* Fixed bug: After fresh C5 install with no demo content - inserting first image, when uploading to filemanager not visible
* Fixed invalid error messages when accessing search interfaces in the dashboard when users didn’t have permission to access them.
* Copied form blocks now work on their target page.
* Copied from blocks can now be edited on their target page.
* Fixed bug where new versions of files incorrectly had the same date added date as old versions.
* Fixed http://www.concrete5.org/developers/bugs/5-7-3-1/content-block-clipboard-custom-classes/
* Fixed https://www.concrete5.org/developers/bugs/5-7-3-1/page-type-permissions-broken-copy-functionality/#698852
* Multiple Google Maps block can now work on the same page (thanks JohnTheFish)
* Fixed typo in user registration notification email (thanks ounziw)
* Fixed http://www.concrete5.org/developers/bugs/5-7-3-1/authentication-type-renders-only-once/ (thanks companyou)
* Fixed https://www.concrete5.org/developers/bugs/5-7-3-1/dashboard-system-section/
* Fixed error when proxy servers send “unknown” instead of an IP address (thanks spainer)
* Fixed bug where an attribute key with the same handle can exist in two categories (thanks Remo)
* Set view theme using setViewTheme() in a package’s on\_before\_render method now correctly sets the theme (Thanks goutnet)
* Fixed potential directory traversal inclusion bug with tools URLs (thanks Egidio Romano of Minded Security)
* Fixed CSRF vulnerability in Dashboard Registrations page; better sanitization of email addresses as well (thanks Egidio Romano of Minded Security)
* Fixed miscellaneous XSS bugs (thanks Mnkras)

## Code & Developer Updates

* Refactored Jobs to work in the new routing system rather than the legacy tools system (thanks Mnkras)
* Updated jQuery to 1.11.2 and jQuery UI to 1.11.4
* Lots of code cleanup (thanks Mnkras)
* jQuery Visualize JavaScript library updated and included in the new Asset System properly (thanks goutnet)
* Custom page type validator class, including a manager with the ability to register custom validators for page types.
* Better driver-based pagination customization API
* New page SEO helper provides a single reliable place to set a pages title, add segments, and more (thanks hissy)
* If developers provide themes with full sample content, they can now provide file manager thumbnails as well, which will improve installation speed and memory footprint.
* Cleaned up outdated and unused files (thanks ezannelli)
* Page templates can now be included in a package in a page_templates/ directory, as well as in the application/ folder (thanks Mesuva)
* ItemList sort API improvements (thanks EC-Joe)
* Lots of better code comments (thanks EC-Joe, EC-Chris)
