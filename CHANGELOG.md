# Development branch

## Feature Updates
* Added new console commands to install, update and remove packages (thanks mlocati)
* Added a new console command to generate and update package translation files (thanks mlocati)
* Added a new console command to batch process packages (remove short tags, compile translations and icons, create zip files) (thanks mlocati)


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
