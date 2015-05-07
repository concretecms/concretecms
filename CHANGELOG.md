# 5.7.4.1

* Fixed inability to save blocks, work with dialogs, do many things while asset caching was enabled (thanks mlocati.)
* Fixed certain panels and dialog windows not opening on Windows servers (thanks mlocati)

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
* Fixed https://github.com/concrete5/concrete5-5.7.0/issues/875

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
