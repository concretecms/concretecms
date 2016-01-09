<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class DashboardManager extends AbstractManager
{

    public function __construct()
    {
        $this->registerMessages(array(
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
            '/dashboard/system/seo/urls' => array(t("Remove index.php from your URLs with pretty URLs, and ensure canonical URLs if you're running a site at multiple domains."), 'dashboard-system-urls'),
            '/dashboard/system/seo/codes' => t("Add any HTML or Javascript code you need for analytics tracking to every page of your site, and pick whether it will go in pages' header or footer. This is where you would input code from Google Analytics, for example."),
            '/dashboard/system/seo/statistics' => t("Turns tracking of page views, file downloads and user registrations on or off. These are displayed on your site's Dashboard > Reports > Statistics page. If your high-traffic site experiences performance issues, you might consider disabling statistics tracking and investigate the use of an alternate, third-party solution for tracking site stats."),
            '/dashboard/system/permissions/site' => t("Control basic, general parameters for viewing and editing your site. Viewing Permissions makes your site's pages accessible to all users, registered-users-only or administrators-only. Edit Access controls which groups can edit pages, when logged in. For more granular control, set permissions on pages individually from Page Properties, or enable Advanced Permissions for even more granular control."),
            '/dashboard/composer/edit' => t("Any attributes or block areas you have enabled to be editable in Composer for this page type are available here. Add blocks to your page type's defaults. After adding the block, click it and choose the option to make it available in Composer."),
            '/dashboard/users/attributes' => t('Store data about your users-- like site preferences, birthdays, bios and more. Control which elements are available for users to update themselves.'),
            '/dashboard/users/add' => t('Manually create new user accounts for your concrete5 site.'),
            '/dashboard' => t('The Dashboard allows you to perform administrative tasks for your site.')        ));
    }


}
