## Installation Instructions for concrete5

	1.	Make sure your config/ directory is writable by a web server. (Note, this is the config/ directory in the root of the archive).
	2.	Make sure files/ and its subdirectories are writable by the Apache process (or the world.)
	3.	Create a new MySQL database and a MySQL user account with the following privileges on that database: INSERT, SELECT, UPDATE, DELETE, CREATE, DROP, ALTER
	4.	Visit your Concrete5 site in your web browser. You should see an installation screen where you can specify your site's name, your base URL, and your database settings.
	5.	Concrete 5 should be installed.
	
You'll probably want to change your admin users password (which can be done in the dashboard). Also check out the settings page in the dashboard

## Simpler Installation

concrete5.org offers hosting and will pre-install and suppport concrete5 for you:

	concrete5 Hosting
	http://www.concrete5.org/hosting/
	
concrete5 can also be installed with one click on other web hosts by using Softaculous or SimpleScripts. Check with your web host to see if you have these services enabled.

## Upgrading from a previous Version of concrete5

	1. In the dashboard of your site, place your site into "Maintenance Mode." This will ensure your site does not appear down while upgrading.
	2. Back up all site data thoroughly, including files and database tables, just in case.
	3. Copy the "concrete" subdirectory from this new archive over your previous directory.
	4. Visit the URL http://yoursite.com/index.php/tools/required/upgrade
	5. You should get some upgrade notes for the current version of Concrete. These might advise you to do current things for upgrading to go smoothly.
	6. Click the upgrade button, and your site should be upgraded!
	
## Credits

	Everyone in the concrete5 core beta team for lots of testing and feedback.

	Everyone who has worked tirelessly to translate concrete5 to different languages - thank you.

	Passionate concrete5 developers and users worldwide.

	File type icons provided by http://Jordan-Michael.com/