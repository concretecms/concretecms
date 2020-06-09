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

* Big thanks to mlocati for delivering a completely new way to specify database XML, built off of the Doctrine DBAL library, including its types and functionality instead of ADODB’s AXMLS. Database XML now has support for foreign keys, comments and more. Doctrine XML is a composer package and can be used by third party projects as well. More information can be found at https://github.com/concrete5/doctrine-xml.
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
