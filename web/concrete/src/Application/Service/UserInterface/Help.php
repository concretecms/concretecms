<?php
namespace Concrete\Core\Application\Service\UserInterface;

use User;

class Help
{
    /**
     * @return array
     */
    public function getBlockTypes()
    {
        $blockTypes = array(
            'autonav' => array(t("Create a navigational menu that reflects the structure of your Sitemap. First, choose the order in which pages appear. Viewing Permissions checks a user's permissions before rendering the link for each page. Display Pages selects the level of the Sitemap where you'd like the menu to begin. Options for displaying sub-pages for each item are also available."), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/auto-nav/'),
            'content' => array(t('Add text content and stylize it using the WYSIWYG editor toolbar. Create links to pages, files and other site assets by using the upper concrete5 toolbar.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/content/'),
            'date_archive' => array(t('Display a list of pages created during a certain month, or months. Pages will be sorted by month.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/date-navigation/'),
            'date_nav' => array(t('Display a list of pages that use a certain page type. Return pages that exist throughout your site, or under only one specific section.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/date-navigation/'),
            'external_form' => array(t('Select a custom-coded form to display as a block on your page.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/external-form/'),
            'file' => array(t('Choose a file from the File Manager and the File block will create a hyperlink to it using the link text you specify.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/file/'),
            'form' => array(t('To begin creating a form, type your question text into the text field, choose the type of answer you need, and whether or not a question response is required when submitting the form. Click Add, then repeat for subsequent questions.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/form/'),
            'google_map' => array(t("Enter a title for your map, then the address of the location you'd like to display on your map. Finally, specify the zoom level of the map to render. Google will try to locate the address automatically when you add the block."), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/google-maps/'),
            'guestbook' => array(t("Enter a title for your guestbook, and adjust the date format to your liking. Choose whether or not to enable comments, moderation and CAPTCHA. Enter an email address if you'd like to be notified of each new guestbook submission."), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/guestbook-comments/'),
            'html' => array(t('Paste your HTML code into this field, and it will be rendered by your web browser.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/html/'),
            'image' => array(t('Select an image from the File Manager, and optionally select a rollover image. Choose where to link the image, if desired, and enter alt text. Use Constrain Image Dimensions to force the image to be displayed at a different size than the actual image file.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/image/'),
            'next_previous' => array(t('This block creates links to adjacent pages on the same level of the Sitemap as the current page. Define custom label text for each link, and choose whether or not to display arrows. The Loop option will display the first page again when a user reaches the last page in the nav.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/next-and-previous-nav/'),
            'page_list' => array(t("The Page List creates a navigation menu that shows one particular level of the Sitemap. Select the Sitemap location that you'd like to display and set Sorting Options to define the order in which pages will be displayed. Truncate Summaries will shorten Page Description text to a specified number of characters."), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/page-list/'),
            'rss_displayer' => array(t('Paste a link to an external RSS feed located on another site, and concrete5 will render it on your page. Select date formatting, feed title, number of items to display at once and choose whether to show or hide article summaries.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/rss-displayer/'),
            'search' => array(t('Create a block to allow users to search the content of your concrete5 site. Choose title, button text, and where concrete5 should search. To submit the form to another page, choose another page from the Sitemap. Place a second Search block on this page and the results will appear here.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/search/'),
            'slideshow' => array(t('Add individual images selected from the File Manager, or choose an existing file set. Playback options allow you to display images in order or randomly. concrete5 will render the images as an animated slideshow.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/slideshow/'),
            'survey' => array(t('Add questions and specify whether or not unregistered users will be allowed to submit responses. Enter each response as its own option under Add Option. Results can be viewed by visiting Dashboard > Reports > Surveys. '), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/survey/'),
            'tags' => array(t('Create a tag cloud that displays all the "Tag" custom attributes set on the current page, or on all pages throughout your site. Enter values into the "Tags" field to automatically add tags to the current page. Link the tags to a specific page by clicking the Advanced tab and using the page picker to select a page.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/tags/'),
            'video' => array(t('Select a video file from the File Manager and specify a width and height at which to display it on your page. AVI, WMV, QuickTime/MPEG4 and FLV formats are supported.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/video-player/'),
            'youtube' => array(t('Paste a short or long-form YouTube link into the YouTube URL field and concrete5 will embed the video on your page. The iFrame option is recommended for best results, especially when displaying pages on mobile devices.'), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/youtube-video/')
        );

        return $blockTypes;
    }

    /**
     * @return array
     */
    public function getPages()
    {
        $pages = array(
            '/dashboard/composer/write' => t('Composer lets you create and publish pages (such as blog posts) directly from your Dashboard. At least one Page Type must be enabled for use Composer, and at least one block needs to be included in Composer via Page Type Defaults.'),
            '/dashboard/composer/drafts' => t('View content created using Composer, but have not yet published. '),
            '/dashboard/sitemap/full' => t("View and change your site's structure. Click a page to see available actions; from this menu, you can control access to or delete a page. Click and drag a page to move it to a different place in your site."),
            '/dashboard/sitemap/explore' => t('View a single branch of your Sitemap. Useful for working with large sites and complex tree structures.'),
            '/dashboard/sitemap/search' => t('Search for pages by name and type. Perform actions on multiple pages simultaneously by checking the boxes next to the pages you want to perform the action on, then selecting the action you want to perform from the drop-down menu.'),
            '/dashboard/files/search' => t("Upload, search, replace, and change files you're using on your site. Change properties and perform basic image editing tasks."),
            '/dashboard/files/attributes' => t('Categorize file attributes into sets to give greater organizational context to editors and enhance ease of use.'),
            '/dashboard/files/sets' => t("File sets provide a flexible way to organize and group your site's assets."),
            '/dashboard/users/search' => t("Search for users by name, email, and group. Perform actions on multiple users simultaneously by checking the boxes next to the users you want to perform the action on, then selecting the action you want to perform from the drop-down menu."),
            '/dashboard/users/groups' => t('View, search for, add, and edit groups. Click a group to edit it. Groups are useful for organizing users and setting permissions for multiple users simultaneously.'),
            '/dashboard/users/add_group' => t('Create a new group to organize and classify users. Choose an expiration date to remove users automatically.'),
            '/dashboard/reports/forms' => t("View responses to your site's forms. Submitted form data is collected here."),
            '/dashboard/reports/statistics' => t('View graphs showing recent site traffic, registration, page creation and downloaded files.'),
            '/dashboard/reports/surveys' => t('The results of completed survey blocks are logged here. Find out what your users have submitted.'),
            '/dashboard/reports/logs' => t('View a record of errors your site has encountered, and a log of outgoing email messages sent by your site.'),
            '/dashboard/pages/themes' => t("Themes change the overall design of your site's pages. Activating a theme will apply it to every page in your site. Preview allows you to see how your content will look when the new theme is applied. Inspect lists the Page Types a theme uses. Customize allows you to change certain properties of your theme's styles, if allowed."),
            '/dashboard/pages/types' => t("Page types display a page's blocks in different ways and can be thought of as 'templates' for your content: Left Sidebar, Full, etc. Page types correspond to specific themes. concrete5 will look for the specified page type in your active theme."),
            '/dashboard/pages/attributes' => t('Control the types of information that can be saved about each page in your site.'),
            '/dashboard/pages/single' => t('Single Pages are used when you want to run custom code in the concrete5 environment. Each Single Page is available in only one location in your sitemap, and this location must be registered from this Dashboard page. This location corresponds to a PHP file placed in your single_pages/ override directory. Below is a list of Single Pages currently installed.'),
            '/dashboard/blocks/stacks' => t('Stacks are groups of blocks, and make it easier to recycle content places on many pages of your site. Stacks are edited and administered from the Dashboard, then placed on individual pages or deployed as Page Type Defaults. Block content and display order can be controlled, as well as the ability to roll-back to previous versions of the stack.'),
            '/dashboard/blocks/types' => t("All blocks currently installed on your site are listed here. Custom blocks that you've developed will appear under Awaiting Installation' until you install them. Click any block to find information about usage and refreshing a block's database tables."),
            '/dashboard/system/environment/logging' => t('View emails your site sends, as well as error messages PHP and MySQL errors returned as your site runs.'),
            '/dashboard/system/environment/debug' => t('Choose between having error messages appear on the page where they occur (as you might want to do while developing a site) and hiding them from site visitors by saving them in your log (as you might want to do if your site is active).'),
            '/dashboard/system/environment/info' => t("View information about your concrete5 site's configuration, as well as details about the hosting environment you're using."),
            '/dashboard/system/attributes/types' => t("Select which types of attributes to make available to pages, users, and files."),
            '/dashboard/system/attributes/sets' => t("Attributes are available to pages (aka Collections), users and files. If you want to add a new attribute to any of these objects, start here."),
            '/dashboard/system/mail/importers' => t("These scripts look for incoming mail to your site. Add-ons like the concrete5 Discussion Forum will attach emails to posts if they match, creating a new page containing the message as reply text."),
            '/dashboard/system/mail/method' => t("Set concrete5 to use an external SMTP server, instead of the default PHP Mail function, for sending email."),
            '/dashboard/system/permissions/blacklist' => t("Block users at specific IP addresses from logging into your site. Blocked users can still view pages that are visible to guests."),
            '/dashboard/system/permissions/tasks' => t("Control users' ability to do perform specific tasks, such as install packages, alter permissions, etc."),
            '/dashboard/system/permissions/file_types' => t("View and change which file types you permit users to upload to your File Manager."),
            '/dashboard/system/permissions/files' => t("Control how users interact with your site's File Manager, allowing or disallowing actions like search, upload, replace and more."),
            '/dashboard/system/basics/editor' => t("Control which set of tools the content-editor toolbar includes (e.g., Simple, Advanced, Office), and the toolbar's spatial dimensions."),
            '/dashboard/system/basics/multilingual' => t("View available language packs installed for the concrete5 Dashboard and editing interface."),
            '/dashboard/system/basics/timezone' => t("Allow your users to specify their time zone. This setting is editable in the user profile and in the dashboard users section."),
            '/dashboard/system/basics/icons' => t("Upload an image that will appear in the address bar when visitors go to your site and in the bookmark list if they bookmark it."),
            '/dashboard/system/basics/name' => t("Even if you change your website's logo, the Site Name is used in some emails."),
            '/dashboard/system/registration/open' => t("Enable Public Registration to let visitors create new user accounts."),
            '/dashboard/system/registration/profiles' => t("Display information about your concrete5 site's users, on a public page."),
            '/dashboard/system/registration/postlogin' => t("Determine where users should be redirected to after they login."),
            '/dashboard/system/environment/storage' => t("Create an alternate file-storage location (in addition to the standard file location) where you'll have the option of putting files after uploading them to the File Manager. "),
            '/dashboard/system/backup/backup' => t("Create a backup of your site's database. This feature works best on small sites. For larger sites, we recommend using a database utility like phpMyAdmin or exporting a database dump via command-line MySQL."),
            '/dashboard/system/backup/update' => t("Download the latest version of concrete5 and upgrade your site."),
            '/dashboard/system/permissions/maintenance_mode' => t("Enable or disable maintenance mode, in which your site is only visible to the admin user. Maintenance Mode is useful for developing, testing or temporarily disabling a site."),
            '/dashboard/system/optimization/jobs' => t("Have concrete5 perform various tasks to help your site running in top condition, process email posts, and update search engine indexing maps. Click the triangle icon next to the job to start it. A success message will be displayed once the job has been completed."),
            '/dashboard/system/optimization/clearcache' => t("If your site is behaving oddly or displaying out-of-date content, it's a good idea to clear the cache. If you're having to clear the cache a lot, you might want to just turn off caching in Cache & Speed Settings."),

            '/dashboard/system/seo/urls' => t("Keep your web page addresses easy for humans and search engines to read by automatically removing references to index.php. You may need to create a file called .htaccess on your server for this to work, but we will try to do it for you first."),
            '/dashboard/system/seo/codes' => t("Add any HTML or Javascript code you need for analytics tracking to every page of your site, and pick whether it will go in pages' header or footer. This is where you would input code from Google Analytics, for example."),
            '/dashboard/system/seo/statistics' => t("Turns tracking of page views, file downloads and user registrations on or off. These are displayed on your site's Dashboard > Reports > Statistics page. If your high-traffic site experiences performance issues, you might consider disabling statistics tracking and investigate the use of an alternate, third-party solution for tracking site stats."),
            '/dashboard/system/permissions/site' => t("Control basic, general parameters for viewing and editing your site. Viewing Permissions makes your site's pages accessible to all users, registered-users-only or administrators-only. Edit Access controls which groups can edit pages, when logged in. For more granular control, set permissions on pages individually from Page Properties, or enable Advanced Permissions for even more granular control."),
            '/dashboard/composer/edit' => t("Any attributes or block areas you have enabled to be editable in Composer for this page type are available here. Add blocks to your page type's defaults. After adding the block, click it and choose the option to make it available in Composer."),
            '/dashboard/users/attributes' => t('Store data about your users-- like site preferences, birthdays, bios and more. Control which elements are available for users to update themselves.'),
            '/dashboard/users/add' => t('Manually create new user accounts for your concrete5 site.'),
            '/dashboard' => t('The Dashboard allows you to perform administrative tasks for your site.')
        );

        // displays views 3 -> 5
        $msgTxt = t("Need help speeding up your site?  contact concrete5's <a href=\"%s\" target=\"_blank\">enterprise services</a>.", 'http://enterprise.concrete5.com/');
        $pages['/dashboard/system/optimization/cache'][] = HelpMessage::get('/page/design', $msgTxt, 3, 5);

        // default message
        $pages['/dashboard/system/optimization/cache'][] = HelpMessage::get('/dashboard/system/optimization/cache', t("The basic cache stores some of concrete5's code in memory. Full page caching saves parts of your website's pages to files on the server. If you are building a new site, it's a good idea to turn the cache off while you are working with code, checking out add-ons and themes, and so on.  Once you feel content creation is your focus, turn caching back on."));

        return $pages;
    }

    /**
     * @return array
     */
    public function getPanels()
    {
        $panels = array(
            '/page/location' => t('Define where this page lives on your website. View and delegate what other pages are redirecting to this page.'),
            '/page/composer' => t('Use the form below to create your page. You can also preview your page in edit mode at any time.'),
            '/page/attributes' => t('Manage the page attributes. To associate an attribute to the page click it in the left panel.'),
            '/page/caching' => t('Full page caching can dramatically improve page speed for pages that don\'t need to have absolutely up-to-the-minute content.')
        );

        return $panels;
    }

    /**
     * @param $type
     * @param $identifier
     * @param $displayCount
     * @return bool|string
     */
    protected function getMessage($type, $identifier, $displayCount)
    {
        $messages = array();
        switch ($type) {
            case 'panel':
                $messages = $this->getPanels();
                break;
            case 'page':
                $messages = $this->getPages();
                break;
            case 'blocktype':
                $messages = $this->getBlockTypes();
                break;
        }

        $message = $messages[$identifier];

        if (is_array($message)) {
            foreach ($message as $m) {
                if ($m instanceof HelpMessage) {
                    $message = $m->getContentToDisplay($displayCount);
                    if ($message) { break; }
                }
            }
        }

        return $message;
    }

    /**
     * @param $type
     * @param $identifier
     */
    protected function getIncrementDisplayCount($type, $identifier)
    {
        /*
         * need to refactor for new session library without multi level arrays
         if (!isset($_SESSION['ccm-help-messages'][$type.'|'.$identifier]['count'])) {
            $_SESSION['ccm-help-messages'][$type.'|'.$identifier]['count'] = 1;
        } else {
            $_SESSION['ccm-help-messages'][$type.'|'.$identifier]['count']++;
        }

        return $_SESSION['ccm-help-messages'][$type.'|'.$identifier]['count'];
        */
    }

    /**
     * @param $type
     * @param $identifier
     * @return void
     */
    public function notify($type, $identifier)
    {
        $message = $this->getMessage($type, $identifier, $this->getIncrementDisplayCount($type, $identifier));
        if (!$message) {
            return false;
        }

        $u = new User();
        if ($u->isRegistered()) {
            $disabledHelpNotifications = $u->config('DISABLED_HELP_NOTIFICATIONS');
            if ($disabledHelpNotifications == 'all') {
                return false;
            } elseif ($disabledHelpNotifications) {
                $disabled = @unserialize($disabledHelpNotifications);
                if (is_array($disabled) && isset($disabled[$type][$identifier])) {
                    return false;
                }
            }
        }

        $ok = t('Ok');
        $hideAll = t('Hide All');
        $html =<<<EOT
        <div class="ccm-notification-help ccm-notification">
            <i class="ccm-notification-icon fa fa-info-circle"></i>
            <div class="ccm-notification-inner dialog-help">{$message}</div>
            <div class="ccm-notification-actions">
                <a href="#" data-help-notification-identifier="{$identifier}" class="ccm-notification-actions-dismiss-single" data-help-notification-type="{$type}" data-dismiss="help-single">{$ok}</a><a href="#" data-help-notification-identifier="{$identifier}" data-help-notification-type="{$type}" data-dismiss="help-all">{$hideAll}</a>
            </div>
        </div>
EOT;
        print $html;
    }

    /**
     * @param User $u
     */
    public function disableAllHelpNotifications(User $u)
    {
        $u->saveConfig('DISABLED_HELP_NOTIFICATIONS', 'all');
    }

    /**
     * @param User $u
     * @param $type
     * @param $identifier
     */
    public function disableThisHelpNotification(User $u, $type, $identifier)
    {
        $disabled = array();
        $message = $this->getMessage($type, $identifier, $this->getIncrementDisplayCount($type, $identifier));
        if ($message) {
            $disabledHelpNotifications = $u->config('DISABLED_HELP_NOTIFICATIONS');
            if ($disabledHelpNotifications && $disabledHelpNotifications != 'all') {
                $disabled = @unserialize($disabledHelpNotifications);
            }
            if (!is_array($disabled)) {
                $disabled = array();
            }
            $disabled[$type][$identifier] = true;
            $u->saveConfig('DISABLED_HELP_NOTIFICATIONS', serialize($disabled));
        }
    }

}

class HelpMessage
{
    /**
     * @var string $identifier unique to the event - typically path of the item the help relates to: /dashboard/users/add
    */
    public $identifier;

    /**
     * @var int $displayOnCountMin - will display on the [x]th call matching the identifier
     */
    public $displayOnCountMin = 0;
    public $displayOnCountMax = 0;

    /**
     * @var string $content - message content that will be displayed in help dialog
     */
    public $content;

    /**
     * @param $displayCount
     * @return bool|string
     */
    public function getContentToDisplay($displayCount)
    {
        $content = false;
        if (($displayCount >= $this->displayOnCountMin && $displayCount <= $this->displayOnCountMax) || $this->displayOnCountMax <= 0) {
            $content = $this->content;
        }

        return $content;
    }

    /**
     * @param string $identifier
     * @param string $content
     * @param int $displayOnCountMin
     * @param int $displayOnCountMax
     * @return HelpMessage
     */
    public static function get($identifier, $content, $displayOnCountMin = 0, $displayOnCountMax = 0)
    {
        $message = new self();
        $message->identifier 	= $identifier;
        $message->content 		= $content;
        $message->displayOnCountMin = $displayOnCountMin;
        $message->displayOnCountMax = $displayOnCountMax;

        return $message;
    }
}
