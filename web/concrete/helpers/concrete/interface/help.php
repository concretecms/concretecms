<?
defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteInterfaceHelpHelper {

	public function getBlockTypes() {
		$blockTypes = array(
		/*	'autonav' => array(t('Auto-nav is great!'), 'http://www.concrete5.org/documentation/editors-guide/in-page-editing/block-areas/add-block/auto-nav/'),
		'content' => t('Content block is great!') */
			
		);
		
		return $blockTypes;
	}
	
	public function getPages() {
		$pages = array(
			'/dashboard/composer/write' => t('Composer lets you prepare content outside the context of your site; similar to most blogging applications.  To use composer, a page type needs to have at least one of its default blocks set to be editable in composer.'),
			'/dashboard/composer/drafts' => t('Pages that you have not published are shown on this page.'),
			'/dashboard/sitemap/full' => t('The sitemap allows you to view your site as a tree and easily organize its hierarchy.  The sitemap is a convenient place to control access through page permissions and also copy, move, or delete pages.\n\n Click a page to see available actions.  Drag a page to move it anywhere in the sitemap.  Concrete5 does not use any arbitrary hierarchies.  Organize your content however you feel. '),
			'/dashboard/sitemap/explore' => t('Sitemap flat view is useful for working with specific branches of your site hierarchy.  All actions in the normal sitemap are available here as well.'),
			'/dashboard/sitemap/search' => t('You can search for pages using a variety of parameters.  Use the checkboxes to select multiple pages and perform the same action on all of them.'),
			'/dashboard/files/search' => t('Add, search, replace and modify the files for your website.  You can also perform some image editing tasks here.'),
			'/dashboard/files/attributes' => t('Files can have additional data attached to them to aid in their organization and modify how they are used as content.'),
			'/dashboard/files/sets' => t('File sets are used extensively by concrete5 and its add-ons to categorize files.'),
			'/dashboard/users/search' => t('Search for users and view / edit their details, or update multiple user accounts at once through bulk actions.'),
			'/dashboard/users/groups' => t('View all your groups, click a group to edit. Groups are used for organization and controlling permissions on your site.'),
			'/dashboard/users/add_group' => t('Name and describe your new group. Group expiration is useful for controlling site access based on time constraints.'),
			'/dashboard/reports/forms' => t('The data from any form blocks on your site will be available here.'),
			'/dashboard/reports/statistics' => t('Site activity at a glance.  For more advanced metrics, be sure to add a tracking code in your System & Settings.'),
			'/dashboard/reports/surveys' => t('Responses to any survey blocks on your site are shown here.'),
			'/dashboard/reports/logs' => t('All outgoing emails and errors can be viewed here.  Always check here when your site is acting up.'),
			'/dashboard/pages/themes' => t('Use a theme to personalize your site.'),
			'/dashboard/pages/types' => t('Pages types let you categorize content and create starting points for the different parts of your site by using default content.  Developers can use page types to stylize concrete5 and control the display of information.'),
			'/dashboard/pages/attributes' => t('Pages can use attributes to supplement content and fine tune page experience.'),
			'/dashboard/pages/single' => t('Single Pages are used by concrete5 for situations where some action needs to happen in only one place.  Most configuration pages are single pages, for example.'),
			'/dashboard/blocks/stacks' => t('Keep small collections of blocks.  Within a stack you can control the presentation order, content and permissions of different blocks you want to use together.  Stacks are like "mini-pages", complete with versioning.\n\n Use stacks on your site to update frequently used content from one place.'),
			'/dashboard/blocks/types' => t('Here is a list of every kind of block on your site.  Developers can use this page to update a block and changes made to its database definition will be noted.  New custom-coded blocks can also be installed here without using the add-on interface.'),
			'/dashboard/system/environment/logging' => t('Turn on logging to capture PHP and MySQL errors, and/or keep a copy of every email the site tries to send. '),
			'/dashboard/system/environment/debug' => t('If you are actively developing, it can be nice to see error messages right on the page. If you\'ve got an active website, you probably want to hide any errors that happen from your audience and just save them in the logs.'),
			'/dashboard/system/backup_restore/database' => t('Use this for development. Also check out the whitelabeling package in the Marketplace for creating site Starting Points. '),
			'/dashboard/system/environment/info' => t("This information helps when submitting a bug report or getting support. It's safe to share with friends: there are no passwords or super secure information in here."),
			'/dashboard/system/attributes/types' => t("Choose which attributes are available to different parts of your concrete5 site. "),
			'/dashboard/system/attributes/sets' => t("Group attributes into sets for easier organization and management."),
			'/dashboard/system/mail/importers' => t("These scripts look for incoming mail to the server. Add-ons like the Discussion Forums will attach emails to posts if they match using a script listed here."),
			'/dashboard/system/mail/method' => t("By default concrete5 uses the webserver's built in PHP Mail function. You can choose to use an external SMTP server instead."),
			'/dashboard/system/permissions/ip_blacklist' => t("You can keep users from logging into your site from specific IP addresss. This won't keep them from viewing your site, just logging in. "),
			'/dashboard/system/permissions/tasks' => t("These permissions control access to parts of the administrative dashboard. If you want to change access to parts of your public site, explore permissions from the page in question."),
			'/dashboard/system/permissions/file_types' => t("Administrators can upload any filetype but avoid whitelisting PHP or EXE files for security."),
			'/dashboard/system/permissions/files' => t("Control who can see which files in the file manager. "),
			'/dashboard/system/basics/editor' => t("Changing the toolbar set changes content block formatting options. "),
			'/dashboard/system/basics/multilingual' => t("Visit the International Community pages on concrete5.org to learn more about using concrete5 in other languages."),
			'/dashboard/system/basics/timezone' => t("Displays site content to site members in their own time zone instead of the servers. "),
			'/dashboard/system/basics/icons' => t("Favicons are shown in visitors' address bars and bookmarks."),
			'/dashboard/system/basics/site_name' => t("Even if you change your website's logo, the Site Name is used in some emails."),
			'/dashboard/system/registration/public_registration' => t("You can turn your concrete5 site into a community by turning on Public Registration and getting some add-ons from the Marketplace"),
			'/dashboard/system/registration/profiles' => t("Allows users to search for eachother and view a member profile page. "),
			'/dashboard/system/registration/postlogin' => t("Determine where users should be redirected to after they login."),
			'/dashboard/system/environment/file_storage_locations' => t("Allows you to setup a secondary storage location.  You can move files to this location after uploading them in the file manager."),
			'/dashboard/system/backup_restore/backup' => t("Use this to export a copy of your database. Be mindful of where you keep these."),
			'/dashboard/system/backup_restore/update' => t("You can perform a one-click upgrade from this page. "),
			'/dashboard/system/permissions/maintenance_mode' => t("When turned on, only the admin account can access your website at all. Everyone else gets a maintenance message. "),
			'/dashboard/system/optimization/jobs' => t("Click \"play\" to run a job right now."),
			'/dashboard/system/optimization/clear_cache' => t("If you have changed something, and you are going crazy because you are not seeing the changes, clear the cache.  When you request support, the first thing a developer will ask you to do is to clear the cache.\n\nSites in development should turn off the cache from Cache & Speed Settings."),
			'/dashboard/system/optimization/cache' => t("The basic cache stores some of concrete5's code in memory. Full page caching saves parts of your website's pages to files on the server. Depending on your system setup, this could help or hurt your performance.  If you are building a new site, it's a good idea to turn the cache off while you are working with code, checking out add-ons and themes, and so on.  Once you feel content creation is your focus, turn caching back on."),
			'/dashboard/system/seo/urls' => t("Keep your web page addresses easy for humans and search engines to read. You may need to create a file called .htaccess on your server for this to work, but we will try to do it for you first."),
			'/dashboard/system/seo/tracking_codes' => t("Insert any HTML or JavaScript code you need on every page for analytics tracking. Google Analytics is free and works well here. "),
			'/dashboard/system/seo/statistics' => t("This stores visitor information in the database. Disabling this may increase site performance, but you will have to get statistics information from elsewhere."),
			'/dashboard/system/permissions/site' => t("The most basic control of permissions. The sitemap can also be used to control permissions.  If more granularity is required research \"advanced permissions\".")
		);
		return $pages;
	}

}
