## Installation Instructions for concrete5

	1.	Make sure your config/ directory is writable by a web server. (Note, this is the config/ directory in the root of the archive).
	2.	Make sure files/ and its subdirectories are writable by the Apache process (or the world.)
	3.	Create a new MySQL database and a MySQL user account with the following privileges on that database: INSERT, SELECT, UPDATE, DELETE, CREATE, DROP, ALTER
	4.	Visit your concrete5 site in your web browser. You should see an installation screen where you can specify your site's name, your base URL, and your database settings.
	5.	concrete5 should be installed.
	
You'll probably want to change your admin users password (which can be done in the dashboard). Also check out the settings page in the dashboard

## Simpler Installation

concrete5.org offers hosting and will pre-install and suppport concrete5 for you:

	concrete5 Hosting
	http://www.concrete5.org/hosting/
	
concrete5 can also be installed with one click on other web hosts by using Softaculous or SimpleScripts. Check with your web host to see if you have these services enabled.

## Upgrading from a previous Version of concrete5

If you want to minimize disk space usage and don't need to use the web interface for update concrete5, you can replace the original concrete directory with a new version, and then upgrade through a particular route.

1. Login as the super user or someone in the Administrators group. You must do this because upgrading a concrete5 site requires that the user attempting to do so have access to the "Upgrade Concrete5" permission. By default this is people in the Administrators group (or the super user.)
2. Download the latest core from http://www.concrete5.org/download
3. Unzip the file.
4. Replace the concrete directory in the web root with the concrete directory you downloaded.
5. Visit the URL http://www.yoursite.com/ccm/system/upgrade. You should see a message about an upgrade being available. Click through to upgrade your database to the most recent version.


## Credits

	Everyone in the concrete5 core beta team for lots of testing and feedback.

	Everyone who has worked tirelessly to translate concrete5 to different languages - thank you.

	Passionate concrete5 developers and users worldwide.

	File type icons provided by http://Jordan-Michael.com/
