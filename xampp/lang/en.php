<?php
	// ---------------------------------------------------------------------
	// GLOBAL
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP for Windows";
	$TEXT['global-showcode']="Show source code";
	$TEXT['global-sourcecode']="Source code";

	// ---------------------------------------------------------------------
	// NAVIGATION
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "Welcome";
	$TEXT['navi-status'] = "Status";
	$TEXT['navi-security'] = "Security";
	$TEXT['navi-doc'] = "Documentation";
	$TEXT['navi-components'] = "Components";
	$TEXT['navi-about'] = "About XAMPP";

	$TEXT['navi-demos'] = "Demos";
	$TEXT['navi-cdcol'] = "CD Collection";
	$TEXT['navi-bio'] = "Biorhythm";
	$TEXT['navi-guest'] = "Guest Book";
	$TEXT['navi-perlenv'] = "MiniPerl";
	$TEXT['navi-iart'] = "Instant Art";
	$TEXT['navi-iart2'] = "Flash Art";
	$TEXT['navi-phonebook'] = "Phone Book";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Writer";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Perl";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "PHP Switch";

	$TEXT['navi-tools'] = "Tools";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Current Guest";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "Languages";

	// ---------------------------------------------------------------------
	// STATUS
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "XAMPP Status";
	$TEXT['status-text1'] = "This page offers you one page to view all information about what's running and working, and what isn't working.";
	$TEXT['status-text2'] = "Some changes to the configuration may sometimes cause false negatives. All reports viewed with SSL (https://localhost) do not function!";

	$TEXT['status-mysql'] = "MySQL database";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perl with mod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-python'] = "Python with mod_python";
	$TEXT['status-mmcache'] = "PHP extension »Turck MMCache«";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-wampp-en.html#mmcache";
	$TEXT['status-smtp'] = "SMTP Service";
	$TEXT['status-ftp'] = "FTP Service";
	$TEXT['status-tomcat'] = "Tomcat Service";
	$TEXT['status-named'] = "Domain Name Service (DNS)";
	$TEXT['status-oci8'] = "PHP extension »OCI8/Oracle«";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp-en.html#oci8";

	$TEXT['status-lookfaq'] = "see FAQ";
	$TEXT['status-ok'] = "ACTIVATED";
	$TEXT['status-nok'] = "DEACTIVATED";

	$TEXT['status-tab1'] = "Component";
	$TEXT['status-tab2'] = "Status";
	$TEXT['status-tab3'] = "Hint";

	// ---------------------------------------------------------------------
	// SECURITY
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "XAMPP SECURITY";
	$TEXT['security-text1'] = "This page gives you a quick overview of the security status of your XAMPP installation. (Please continue reading after the table.)";
	$TEXT['security-text2'] = "The green marked points are secure; the red marked points are definitively unsecure and the yellow marked points couldn't be checked (for example because the sofware to check isn't running).<p>To fix the problems for mysql, phpmyadmin and the xampp directory simply use</b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[allowed only for localhost]<br>&nbsp;<br>&nbsp;<br>
	Some other important notes:<ul>
	<li>All these test are made available ONLY to the host \"localhost\" (127.0.0.1).</li>
	<li><i><b>For FileZilla FTP und Mercury Mail, you must fix all security problems by yourself! Sorry. </b></i></li>
	<li>If your computer is blocked by a firewall or not online, your servers are SECURE against outside attacks.</li>
	<li>If servers are not running, they are also SECURE!</li></ul>";
	$TEXT['security-text3'] = "<b>Please consider this:
	With more XAMPP security, some examples will NOT execute error free. If you use PHP in \"safe mode\" for example, some functions of this security frontend will not work anymore. Often more security means less functionality.</b>";
	$TEXT['security-text4'] = "The XAMPP default ports:";

	$TEXT['security-ok'] = "SECURE";
	$TEXT['security-nok'] = "UNSECURE";
	$TEXT['security-noidea'] = "UNKNOWN";

	$TEXT['security-tab1'] = "Subject";
	$TEXT['security-tab2'] = "Status";

	$TEXT['security-checkapache-nok'] = "These XAMPP pages are accessible through the network by anyone";
	$TEXT['security-checkapache-ok'] = "These XAMPP pages are not accessible through the network by anyone";
	$TEXT['security-checkapache-text'] = "Every XAMPP demo page you are looking at right now is accessible by everyone over the network. Everyone who knows your IP address can see these pages.";

	$TEXT['security-checkmysqlport-nok'] = "MySQL is accessible through the network";
	$TEXT['security-checkmysqlport-ok'] = "MySQL is no longer accessible over the network";
	$TEXT['security-checkmysqlport-text'] = "This is a potential or at least theoretical security leak; If you're mad about security you should disable the network interface of MySQL.";

	$TEXT['security-checkpmamysqluser-nok'] = "The phpMyAdmin user pma has no password";
	$TEXT['security-checkpmamysqluser-ok'] = "The phpMyAdmin user pma has a password.";
	$TEXT['security-checkpmamysqluser-text'] = "phpMyAdmin saves your preferences in an extra MySQL database. To access this data phpMyAdmin uses the special user pma. In the default installation, this user has no password set and to avoid security problems you should provide a password.";

	$TEXT['security-checkmysql-nok'] = "The MySQL admin user root has NO password";
	$TEXT['security-checkmysql-ok'] = "The MySQL admin user root has a password";
	$TEXT['security-checkmysql-out'] = "The MySQL server is not running or is blocked by a firewall!";
	$TEXT['security-checkmysql-text'] = "Every local user on this Windows machine can access your MySQL database with administrator rights. You should set a password.";

	$TEXT['security-pop-nok'] = "The test user (newuser) for Mercury Mail server (POP3) has an old password (wampp)";
	$TEXT['security-pop-ok'] = "The test user \"newuser\" for the POP3 server (Mercury Mail?) does not exist anymore or has a new password";
	$TEXT['security-pop-out'] = "A POP3 server like Mercury Mail is not running or is blocked by a firewall!";
	$TEXT['security-pop-notload'] = "<i>The necessary IMAP extension for this secure test is not loading (php.ini)!</i><br>";
	$TEXT['security-pop-text'] = "Please check and perhaps edit all users and passwords in the the Mercury Mail server configuration!";

	$TEXT['security-checkftppassword-nok'] = "The FileZilla FTP password is still 'wampp'";
	$TEXT['security-checkftppassword-ok'] = "The FileZilla FTP password was changed";
	$TEXT['security-checkftppassword-out'] = "A FTP server is not running  or is blocked by a firewall!";
	$TEXT['security-checkftppassword-text'] = "If the FileZilla FTP server was started, the default user 'newuser' with password 'wampp' can upload and change files for your XAMPP webserver. So if you enabled FileZilla FTP you should set a new password for user 'newuser'.";

	$TEXT['security-phpmyadmin-nok'] = "PhpMyAdmin is freely accessible by network";
	$TEXT['security-phpmyadmin-ok'] = "PhpMyAdmin password login is enabled.";
	$TEXT['security-phpmyadmin-out'] = "PhpMyAdmin: Could not find the 'config.inc.php' ...";
	$TEXT['security-phpmyadmin-text'] = "PhpMyAdmin is accessible by network without a password. The configuration 'httpd' or 'cookie' in the \"config.inc.php\" can help.";

	$TEXT['security-checkphp-nok'] = "PHP is NOT running in \"safe mode\"";
	$TEXT['security-checkphp-ok'] = "PHP is running in \"safe mode\"";
	$TEXT['security-checkphp-out'] = "Unable to control the setting of PHP!";
	$TEXT['security-checkphp-text'] = "If you do not want to offer PHP execution for users outside of this server, please think about a \"safe mode\" configuration. But for the  standalone developer we recommend NOT turning on \"safe mode\" configuration because some important functions will not work. <a href=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>More Info</font></a>";


	// ---------------------------------------------------------------------
	// SECURITY SETUP
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "Security console MySQL & XAMPP directory protection";
	$TEXT['mysql-rootsetup-head'] = "MYSQL SECTION: \"ROOT\" PASSWORD";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "The MySQL server is not running or is blocked by a firewall! Please check this problem first ...";
	$TEXT['mysql-rootsetup-passwdnotok'] = "The new password is identical with the repeat password. Please enter both passwords again!";
	$TEXT['mysql-rootsetup-passwdnull'] = "Blank passwords ('') will not be accepted!";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "SUCCESS: The password for the SuperUser 'root' was set or updated!
	But note: The initialization of the new password for \"root\" needs a RESTART OF MYSQL !!!! The data with the new password was safed in the following file:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "ERROR: The root password is wrong. MySQL declined the login with the current root password.";
	$TEXT['mysql-rootsetup-passwdold'] = "Current password:";
	$TEXT['mysql-rootsetup-passwd'] = "New password:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "Repeat the new password:";
	$TEXT['mysql-rootsetup-passwdchange'] = "Password changing";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "PhpMyAdmin authentication:";

	$TEXT['xampp-setup-head'] = "XAMPP DIRECTORY PROTECTION (.htaccess)";
	$TEXT['xampp-setup-user'] = "User:";
	$TEXT['xampp-setup-passwd'] = "Password:";
	$TEXT['xampp-setup-start'] = "Secure the XAMPP directory";
	$TEXT['xampp-setup-notok'] = "<br><br>ERROR: The string for the user name and password must be 3 to 15 characters long. Special characters like <öäü (usw.) and empty characters are not allowed!<br><br>";
	$TEXT['xampp-setup-ok'] = "<br><br>The root password was successfully changed. Please restart MYSQL to enable these changes!<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>SUCCESS: The XAMPP directory is protected now! All personal data was saved in the following file:<br>";
	$TEXT['xampp-config-notok'] = "<br><br>ERROR: Your system could NOT activate directory protection with the \".htaccess\" and \"htpasswd.exe\". Perhaps PHP is in \"Safe Mode\". <br><br>";

	// ---------------------------------------------------------------------
	// START
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "Welcome to XAMPP for Windows";

	$TEXT['start-subhead'] = "Congratulations:<br>You have successfully installed XAMPP on this system!";

	$TEXT['start-text1'] = "Now you can start using Apache and Co. You should first try »Status« on the left navigation to make sure everything works fine.";

	$TEXT['start-text2'] = "";

	$TEXT['start-text3'] = "";

	$TEXT['start-text4'] = "For OpenSSL support please use the test certificate with <a href='https://127.0.0.1' target='_top'>https://127.0.0.1</a> or <a href='https://localhost' target='_top'>https://localhost</a>";

	$TEXT['start-text5'] = "For this release a special thanks to <a href=\"http://www.php.net/credits.php\" target=\"_new\">Uwe Steinmann</a> for his excellent development and compilation of all current \"Special\" modules!";

	$TEXT['start-text6'] = "Good luck, Kay Vogelgesang + Kai 'Oswald' Seidler";

	// ---------------------------------------------------------------------
	// MANUALS
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "Online documentation";

	$TEXT['manuals-text1'] = "XAMPP combines many different sofware packages into one package. Here's a list of standard and reference documentation of the most important packages.";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/en/\">Apache 2 documentation</a>
	<li><a href=\"http://www.php.net/manual/en/\">PHP <b>referenz </b>documentation</a>
	<li><a href=\"http://perldoc.perl.org/\">Perl documentation</a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">MySQL documentation</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccelerator</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">FPDF Class documentation</a>
	</ul>";

	$TEXT['manuals-text2'] = "And a small list of tutorials and the Apache Friends documentation page:";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/en/faq-xampp.html\">Apache Friends documentation</a>
	<li><a href=\"http://www.freewebmasterhelp.com/tutorials/php/\">PHP Tutorial</a> by David Gowans
	<li><a href=\"http://www.davesite.com/webstation/html/\">HTML - An Interactive Tutorial For Beginners</a> by Dave Kristula
	<li><a href=\"http://www.comp.leeds.ac.uk/Perl/start.html\">Perl Tutorial</a> by Nik Silver
	</ul>";

	$TEXT['manuals-text3'] = "Good luck and have fun! :)";

	// ---------------------------------------------------------------------
	// COMPONENTS
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "XAMPP components";

	$TEXT['components-text1'] = "XAMPP combines many different sofware packages into one package. Here's an overview of all packages.";

	$TEXT['components-text2'] = "Many thanks to the developers of these programs.";

	$TEXT['components-text3'] = "In the directory <b>\\xampp\licenses</b> you will find all licenses files of these programs.";

	// ---------------------------------------------------------------------
	// CD COLLECTION DEMO
	// ---------------------------------------------------------------------

	$TEXT['cds-head'] = "CD Collection (Example for PHP+MySQL+PDF Class)";
	$TEXT['cds-head-fpdf'] = "CD Collection (Example for PHP+MySQL+FPDF)";

	$TEXT['cds-text1'] = "A very simple CD programm.";

	$TEXT['cds-text2'] = "CD list as <a href='$_SERVER[PHP_SELF]?action=getpdf'>PDF document</a>.";

	$TEXT['cds-error'] = "Could not connect to database!<br>Is MySQL running or did you change the password?";
	$TEXT['cds-head1'] = "My CDs";
	$TEXT['cds-attrib1'] = "Artist";
	$TEXT['cds-attrib2'] = "Title";
	$TEXT['cds-attrib3'] = "Year";
	$TEXT['cds-attrib4'] = "Command";
	$TEXT['cds-sure'] = "Sure?";
	$TEXT['cds-head2'] = "Add CD";
	$TEXT['cds-button1'] = "DELETE CD";
	$TEXT['cds-button2'] = "ADD CD";

	// ---------------------------------------------------------------------
	// BIORHYTHM DEMO
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "Biorhythm (Example for PHP+GD)";

	$TEXT['bio-by'] = "by";
	$TEXT['bio-ask'] = "Please enter your date of birth";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "Date";
	$TEXT['bio-error2'] = "is invalid";

	$TEXT['bio-birthday'] = "Birthday";
	$TEXT['bio-today'] = "Today";
	$TEXT['bio-intellectual'] = "Intellectual";
	$TEXT['bio-emotional'] = "Emotional";
	$TEXT['bio-physical'] = "Physical";

	// ---------------------------------------------------------------------
	// INSTANT ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "Instant Art (Example for PHP+GD+FreeType)";
	$TEXT['iart-text1'] = "Font »AnkeCalligraph« by <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// FLASH ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Flash Art (Example for PHP+MING)";
	$TEXT['flash-text1'] = "The MING project for win32 does not exist any longer and it is not complete.<br>Please read this: <a class=blue target=extern href=\"http://www.opaque.net/wiki/index.php?Ming\">Ming - an SWF output library and PHP module</a>";
	$TEXT['flash-ok'] = "OK";

	// ---------------------------------------------------------------------
	// PHONE BOOK DEMO
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "Phone Book (Example for PHP+SQLite)";

	$TEXT['phonebook-text1'] = "A very simple phone book script. But implemented with a very modern and up-to-date technology: SQLite, the SQL database without server.";

	$TEXT['phonebook-error'] = "Couldn't open the database!";
	$TEXT['phonebook-head1'] = "My phone numbers";
	$TEXT['phonebook-attrib1'] = "Last name";
	$TEXT['phonebook-attrib2'] = "First name";
	$TEXT['phonebook-attrib3'] = "Phone number";
	$TEXT['phonebook-attrib4'] = "Command";
	$TEXT['phonebook-sure'] = "Sure?";
	$TEXT['phonebook-head2'] = "Add entry";
	$TEXT['phonebook-button1'] = "DELETE";
	$TEXT['phonebook-button2'] = "ADD";

	// ---------------------------------------------------------------------
	// ABOUT
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "About XAMPP";

	$TEXT['about-subhead1'] = "Idea and realisation";

	$TEXT['about-subhead2'] = "Design";

	$TEXT['about-subhead3'] = "Collaboration";

	$TEXT['about-subhead4'] = "Contact persons";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "Mailing with Mercury Mail SMTP and POP3 Server";
	$TEXT['mail-hinweise'] = "Some important notes for using Mercury!";
	$TEXT['mail-adress'] = "Sender:";
	$TEXT['mail-adressat'] = "Recipient:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "Subject:";
	$TEXT['mail-message'] = "Message:";
	$TEXT['mail-sendnow'] = "This message is sending now ...";
	$TEXT['mail-sendok'] = "The message was successfully sent!";
	$TEXT['mail-sendnotok'] = "Error! The message was not successfully sent!";
	$TEXT['mail-help1'] = "Notes for using Mercury:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>Mercury needs an external connection on startup;</li>
	<li>on startup, Mercury defines the Domain Name Service (DNS) automatically as the name server of your provider;</li>
	<li>For all user of gateway servers: Please set your DNS via TCP/IP (f.e. via InterNic with the IP number 198.41.0.4);</li>
	<li>the config file of Mercury is called MERCURY.INI;</li>
	<li>to test, please send a message to postmaster@localhost or admin@localhost and check for these messages in the following folders: xampp.../mailserver/MAIL/postmaster or (...)/admin;</li>
	<li>one test user called \"newuser\" (newuser@localhost) with the Password = wampp;</li>
	<li>spam and other obscenities are totally forbidden with Mercury!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "FileZilla FTP Server";
	$TEXT['filezilla-install'] = "Apache is <U>not</U> a FTP Server ... but FileZilla FTP is one. Please consider the following references.";
	$TEXT['filezilla-install2'] = "Into the main directory of xampp, start \"filezilla_setup.bat\" for setup. Attention: For Windows NT, 2000 and XP Professional, FileZilla needs to install as service.";
	$TEXT['filezilla-install3'] = "Configure \"FileZilla FTP\". For this, please use the FileZilla Interface with the \"FileZilla Server Interface.exe\". Two Users are in this example:<br><br>
	A: A default user \"newuser\", password \"wampp\". The home directory is xampp\htdocs.<br>
	B: An anonymous user \"anonymous\", no password. The home directory is xampp\anonymous.<br><br>
	The default interface is the loopback address 127.0.0.1.";
	$TEXT['filezilla-install4'] = "The FTP Server is shutdown with the \"FileZillaFTP_stop.bat\". For FileZilla FTP as service, please use the \"FileZillaServer.exe\" directly. Then, you can configure all start options.";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "Excel export with PEAR (PHP)";
	$TEXT['pear-text'] = "A short <a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">Manual</A> from Björn Schotte of <a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A> (only in german)";
	$TEXT['pear-cell'] = "The value of a cell";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Graph Library for PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - Another DB access (PHP)";
	$TEXT['ADOdb-text'] = "ADOdb stands for Active Data Objects Data Base. We currently support MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite and generic ODBC. The Sybase, Informix, FrontBase and PostgreSQL drivers are community contributions. You find it here at \(mini)xampp\php\pear\adodb.";
	$TEXT['ADOdb-example'] = "The example:";
	$TEXT['ADOdb-dbserver'] = "Database server (MySQL, Oracle ..?)";
	$TEXT['ADOdb-host'] = "Host of the DB server (name or IP)";
	$TEXT['ADOdb-user'] = "Username ";
	$TEXT['ADOdb-password'] = "Password";
	$TEXT['ADOdb-database'] = "Current database on this database server";
	$TEXT['ADOdb-table'] = "Selected table of database";
	$TEXT['ADOdb-nottable'] = "<p><b>Table not found!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>The driver for this database server does not exists or perhaps it is an ODBC, ADO or OLEDB driver!</b>";


	// ---------------------------------------------------------------------
	// INFO
	// ---------------------------------------------------------------------

	$TEXT['info-package'] = "Package";
	$TEXT['info-pages'] = "Pages";
	$TEXT['info-extension'] = "Extensions";
	$TEXT['info-module'] = "Apache module";
	$TEXT['info-description'] = "Description";
	$TEXT['info-signature'] = "Signature";
	$TEXT['info-docdir'] = "Document root";
	$TEXT['info-port'] = "Default port";
	$TEXT['info-service'] = "Services";
	$TEXT['info-examples'] = "Examples";
	$TEXT['info-conf'] = "Configuration files";
	$TEXT['info-requires'] = "Requires";
	$TEXT['info-alternative'] = "Alternative";
	$TEXT['info-tomcatwarn'] = "Warning! Tomcat is not started on port 8080.";
	$TEXT['info-tomcatok'] = "OK! Tomcat is started on port 8080 successfully.";
	$TEXT['info-tryjava'] = "The java example (JSP) with Apache MOD_JK.";
	$TEXT['info-nococoon'] = "Warning! Tomcat is not started on port 8080. Cannot install
	\"Cocoon\" without running Tomcat server!";
	$TEXT['info-okcocoon'] = "Ok! The Tomcat is running normaly. The installation works can last some minutes! To install \"Cocoon\" now click here ...";

	// ---------------------------------------------------------------------
	// PHP Switch
	// ---------------------------------------------------------------------

	$TEXT['switch-head'] = "PHP Switch 1.0 win32 for XAMPP";
	$TEXT['switch-phpversion'] = "<i><b>Current PHP version in THIS XAMPP is ";
	$TEXT['switch-whatis'] = "<b>What is the PHP switch?</b><br>The apachefriends PHP Switch is for switching XAMPP between the PHP version 4, version 5, AND (!) back. So you can test your scripts with PHP 4 or PHP 5.<p>";
	$TEXT['switch-find'] = "<b>Where is the PHP Switch?</b><br>PHP Switch for XAMPP will execute a PHP file (XAMPP install folder) with the name \"php-switch.php\". You should use this batch file for executing: ";
	$TEXT['switch-care'] = "<b>What can go wrong?</b><br>PHP Switch will not change your PHP version, when a) the Apache HTTPD is running or/and b) the \".phpversion\" file in the install folder is vacant or has a bug. In the \".phpversion\", there was written the XAMPP current main PHP version number like \"4\" or \"5\". Please beginn with a \"shutdown\" for the Apache HTTPD and THEN execute the  \"php-switch.bat\".<p>";
	$TEXT['switch-where4'] = "<b>After that, where can I locate my (old) config files?</b><br><br>For PHP 4:<br>";
	$TEXT['switch-where5'] = "<br><br>For PHP 5:<br>";
	$TEXT['switch-make1'] = "<b>What is with changes in my config files?</b><br><br>There lives! For PHP4 or PHP5 in the<br>";
	$TEXT['switch-make2'] = "<br><br> .. secured for PHP4 ...<br>";
	$TEXT['switch-make3'] = "<br><br> .. secured for PHP5 ...<br>";
	$TEXT['switch-make4'] = "<br><br>And these files are going back with the PHP switching!!<p>";
	$TEXT['switch-not'] = "<b>My PHP is okay AND i will NOT a \"switch\" !!!</b><br>Super! Then forget this here ... ;-)<br>";

	// ---------------------------------------------------------------------
	// Cocoon
	// ---------------------------------------------------------------------

	$TEXT['go-cocoon'] = "Cocoon now with http://localhost/cocoon/";
	$TEXT['path-cocoon'] = "And the correct folder on your disk is: ...\\xampp\\tomcat\\webapps\\cocoon";

	// ---------------------------------------------------------------------
	// Guest
	// ---------------------------------------------------------------------

	$TEXT['guest1-name'] = "Current Guest in this release: <i>FCKeditor</i>";
	$TEXT['guest1-text1'] = "A very nice HMTL ONLINE editor with much more JavaScript. Optimized for the IE. But do not function with the Mozilla FireFox.";
	$TEXT['guest1-text2'] = "FCKeditor Homepage: <a href=\"http://www.fckeditor.net\" target=\"_new\">www.fckeditor.net</a>. Note: The Arial font do NOT function here, but i do not know why!";
	$TEXT['guest1-text3'] = "<a href=\"guest-FCKeditor/fckedit-dynpage.php\" target=\"_new\">The example page written with the FCKeditor.</A>";
 
	// ---------------------------------------------------------------------
	// NAVI SPECIALS SECTION
	// ---------------------------------------------------------------------
	
	$TEXT['navi-specials'] = "Specials";
	
    // ---------------------------------------------------------------------
	// PS AND PARADOX EXAMPLE
	// ---------------------------------------------------------------------

    $TEXT['navi-ps'] = "PHP PostScript";
	$TEXT['ps-head'] = "PostScript Module Example";
	$TEXT['ps-text1'] = "PostScript Module »php_ps« by <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['ps-text2'] = "Tip: To convert PS files to PDF files on win32, you can use <a href=\"http://www.shbox.de/\" target=\"_new\">FreePDF</a> with <a href=\"http://www.ghostscript.com/awki/\" target=\"_new\">GhostScript</a>.";
	
	$TEXT['navi-paradox'] = "PHP Paradox";
	$TEXT['paradox-head'] = "Paradox Module Example";
	$TEXT['paradox-text1'] = "Paradox Module »php_paradox« by <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['paradox-text2'] = "<h2>Reading and writing a paradox database</h2>";
	$TEXT['paradox-text3'] = "More examples you can find in the directory ";
	$TEXT['paradox-text4'] = "Further information to Paradox databases in <a href=\"http://en.wikipedia.org/wiki/Paradox\" target=\"_new\">WikiPedia</a>.";
?>
