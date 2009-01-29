<?php
	// ---------------------------------------------------------------------
	// GLOBAL
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP pour Windows";
	$TEXT['global-showcode'] = "Obtenir les sources ici";
	$TEXT['showcode'] = "Sources";

	// ---------------------------------------------------------------------
	// NAVIGATION
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "Bienvenue";
	$TEXT['navi-status'] = "Statut";
	$TEXT['navi-security'] = "Sécurité";
	$TEXT['navi-doc'] = "Documentation";
	$TEXT['navi-components'] = "Composants";
	$TEXT['navi-about'] = "A propos de XAMPP";

	$TEXT['navi-demos'] = "Démos";
	$TEXT['navi-cdcol'] = "Collection de CD";
	$TEXT['navi-bio'] = "Biorhythme";
	$TEXT['navi-guest'] = "Guest Book";
	$TEXT['navi-perlenv'] = "MiniPerl";
	$TEXT['navi-iart'] = "Instant Art";
	$TEXT['navi-iart2'] = "Flash Art";
	$TEXT['navi-phonebook'] = "Répertoire<br>Téléphonique";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Writer";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Module Perl";
	$TEXT['navi-sms'] = "SMS Api";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "PHP Switch";

	$TEXT['navi-tools'] = "Outils";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-phpsqliteadmin'] = "phpSQLiteAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Invité";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "Langues";

	// ---------------------------------------------------------------------
	// STATUS
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "Statut XAMPP";
	$TEXT['status-text1'] = "Cette page recueille l'ensemble des informations sur ce qui marche et qui est lancé et ce qui ne l'est pas.";
	$TEXT['status-text2'] = "Certains changements dans la configuration peuvent causer de faux rapports de statuts erronés. Avec SSL (https://localhost) tous ces rapports de fonctionnent pas!";

	$TEXT['status-mysql'] = "Base de Données MySQL";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perl avec mod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-mmcache'] = "PHP extension \"Turck MMCache\"";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-wampp-en.html#mmcache";
	$TEXT['status-smtp'] = "Serveur SMTP";
	$TEXT['status-ftp'] = "Serveur FTP";
	$TEXT['status-oci8'] = "PHP extension \"OCI8/Oracle\"";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp-en.html#oci8";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-tomcat'] = "Tomcat Service";
	$TEXT['status-named'] = "Domain Name Service (DNS)";
	$TEXT['status-python'] = "Python avec mod_python";


	$TEXT['status-lookfaq'] = "voir FAQ";
	$TEXT['status-ok'] = "ACTIVE";
	$TEXT['status-nok'] = "DESACTIVE";

	$TEXT['status-tab1'] = "Composant";
	$TEXT['status-tab2'] = "Statut";
	$TEXT['status-tab3'] = "Conseil";

	// ---------------------------------------------------------------------
	// SECURITY
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "Securité XAMPP";
	$TEXT['security-text1'] = "Cette page vous donne un bref aperçu du statut de securité de votre installation de XAMPP. (Veuillez lire la suite après le tableau.) <i>Sorry, but no french translation for this section available, so switching to english.</i>";
	$TEXT['security-text2'] = "The green marked points are secure; the red marked points are definitively unsecure and the yellow marked points couldn't be checked (for example because the sofware to check isn't running).<p>To fix the problems for mysql, phpmyadmin and the xampp directory simply use</b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[allowed only for localhost]<br>&nbsp;<br>&nbsp;<br>
	Some other important notes:<ul>
	<li>All these test are made ONLY for host \"localhost\" (127.0.0.1).</li>
	<li><i><b>For FileZilla FTP und Mercury Mail, you must fix all security problems by yourself! Sorry. </b></i></li>
	<li>If your computer is not online or blocked by a firewall, your servers are SECURE against outside attacks.</li>
	<li>If servers are not running, these servers are although SECURE!</li></ul>";
	$TEXT['security-text3'] = "<b>Please consider this:
	With more XAMPP security some examples will NOT execute error free. If you use PHP in \"safe mode\" for example some functions of this security frontend will not working anymore. Often even more security means less functionality at the same time.</b>";
	$TEXT['security-text4'] = "The XAMPP default ports:";

	$TEXT['security-ok'] = "SECURE";
	$TEXT['security-nok'] = "UNSECURE";
	$TEXT['security-noidea'] = "UNKNOWN";

	$TEXT['security-tab1'] = "Subject";
	$TEXT['security-tab2'] = "Status";

	$TEXT['security-checkapache-nok'] = "These XAMPP pages are accessible by network for everyone";
	$TEXT['security-checkapache-ok'] = "These XAMPP pages are no longer accessible by network for everyone";
	$TEXT['security-checkapache-text'] = "Every XAMPP demo page you are right now looking at is accessible for everyone over network. Everyone who knows your IP address can see these pages.";

	$TEXT['security-checkmysqlport-nok'] = "MySQL is accessible by the network";
	$TEXT['security-checkmysqlport-ok'] = "MySQL is no longer accessible over the network";
	$TEXT['security-checkmysqlport-text'] = "This is a potential or at least theoretical security leak. And if you're mad about security you should disable the network interface of MySQL.";

	$TEXT['security-checkpmamysqluser-nok'] = "The phpMyAdmin user pma has no password";
	$TEXT['security-checkpmamysqluser-ok'] = "The phpMyAdmin user pma has no longer no password";
	$TEXT['security-checkpmamysqluser-text'] = "phpMyAdmin saves your preferences in an extra MySQL database. To access this data phpMyAdmin uses the special user pma. This user has in the default installation no password set and to avoid any security problems you should give him a passwort.";

	$TEXT['security-checkmysql-nok'] = "The MySQL admin user root has NO password";
	$TEXT['security-checkmysql-ok'] = "The MySQL admin user root has no longer no password";
	$TEXT['security-checkmysql-out'] = "A MySQL server is not running  or is blocked by a firewall!";
	$TEXT['security-checkmysql-text'] = "Every local user on Windows box can access your MySQL database with administrator rights. You should set a password.";

	$TEXT['security-pop-nok'] = "The test user (newuser) for Mercury Mail server (POP3) have an old password (wampp)";
	$TEXT['security-pop-ok'] = "The test user \"newuser\" for the POP3 server (Mercury Mail?) does not exists anymore or have a new password";
	$TEXT['security-pop-out'] = "A POP3 server like Mercury Mail is not running or is blocked by a firewall!";
	$TEXT['security-pop-notload'] = "<i>The necessary IMAP extension for this secure test is not loading (php.ini)!</i><br>";
	$TEXT['security-pop-text'] = "Please check and perhaps edit all users and passwords in the the Mercury Mail server configuration!";

	$TEXT['security-checkftppassword-nok'] = "The FileZilla FTP password is still 'wampp'";
	$TEXT['security-checkftppassword-ok'] = "The FileZilla FTP password was changed";
	$TEXT['security-checkftppassword-out'] = "A FTP server is not running  or is blocked by a firewall!";
	$TEXT['security-checkftppassword-text'] = "If the FileZilla FTP server was started, the default user 'newuser' with password 'wampp' can upload and change files for your XAMPP webserver. So if you enabled FileZilla FTP you should set a new password for user 'newuser'.";

	$TEXT['security-phpmyadmin-nok'] = "PhpMyAdmin is free accessible by network";
	$TEXT['security-phpmyadmin-ok'] = "PhpMyAdmin password login is enabled.";
	$TEXT['security-phpmyadmin-out'] = "PhpMyAdmin: Could not find the 'config.inc.php' ...";
	$TEXT['security-phpmyadmin-text'] = "PhpMyAdmin is accessible by network without password. The configuration 'httpd' or 'cookie' in the \"config.inc.php\" can help.";

	$TEXT['security-checkphp-nok'] = "PHP is NOT running in \"safe mode\"";
	$TEXT['security-checkphp-ok'] = "PHP is running in \"safe mode\"";
	$TEXT['security-checkphp-out'] = "Unable to control the setting of PHP!";
	$TEXT['security-checkphp-text'] = "If do you want to offer PHP executions for outside persons, please think about a \"safe mode\" configuration. But for standalone developer we recommend NOT the \"safe mode\" configuration because some important functions will not working then. <a href=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>More Info</font></a>";

	// ---------------------------------------------------------------------
	// SECURITY SETUP
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "Security console MySQL & XAMPP directory protection";
	$TEXT['mysql-rootsetup-head'] = "MYSQL SECTION: \"ROOT\" PASSWORD";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "The MySQL server is not running or is blocked by a firewall! Please check this problem first ...";
	$TEXT['mysql-rootsetup-passwdnotok'] = "The new password is identical with the repeat password. Please enter both passwords for new!";
	$TEXT['mysql-rootsetup-passwdnull'] = "Zero passwords ('') will not accepted!";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "SUCCESS: The password for the SuperUser 'root' was set or updated!
	But note: The initialization of the new password for \"root\" needs a RESTART OF MYSQL !!!! The data with the new password was safed in the following file:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "ERROR: The root password is perhaps wrong. MySQL decline the login with these current root password.";
	$TEXT['mysql-rootsetup-passwdold'] = "Current passwort:";
	$TEXT['mysql-rootsetup-passwd'] = "New password:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "Repeat the new password:";
	$TEXT['mysql-rootsetup-passwdchange'] = "Password changing";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "PhpMyAdmin authentification:";

	$TEXT['xampp-setup-head'] = "XAMPP DIRECTORY PROTECTION (.htaccess)";
	$TEXT['xampp-setup-user'] = "User:";
	$TEXT['xampp-setup-passwd'] = "Password:";
	$TEXT['xampp-setup-start'] = "Make safe the XAMPP directory";
	$TEXT['xampp-setup-notok'] = "<br><br>ERROR: The string for the user name and password must have at least three  characters and not more then 15 characters. Special characters like <öäü (usw.) and empty characters are not allowed!<br><br>";
	$TEXT['xampp-setup-ok'] = "<br><br>The root password was successfully changed. Please restart MYSQL for loading these changes!<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>SUCCESS: The XAMPP directory is protected now! All personal data was safed in the following file:<br>";
	$TEXT['xampp-config-notok'] = "<br><br>ERROR: Your system could NOT activate the directory protection with the \".htaccess\" and the \"htpasswd.exe\". Perhaps PHP is in the \"Safe Mode\".<br><br>";

	// ---------------------------------------------------------------------
	// START
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "Bienvenue dans XAMPP pour Windows";

	$TEXT['start-subhead'] = "Bravo:<br>Vous venez d'installer XAMPP avec succès!";

	$TEXT['start-text-newest'] = "";

	$TEXT['start-text1'] = "Vous pouvez dès lors commencer à utiliser Apache and Co. Vous devriez avant tout essayer \"Statut\" dans le menu de navigation pour s'assurer que tout fonctionne bien.";

	$TEXT['start-text2'] = "Après les tests, vous pouvez aller voir les exemples situés sous les liens de test.";

	$TEXT['start-text3'] = "Avant de commencer à programmer en PHP ou en Perl (ou autre ;),  allez voir le fichier <a href=\"http://www.apachefriends.org/wampp-en.html\">XAMPP lisez-moi</a> pour avoir plus d'informations sur votre installation XAMPP.";

	$TEXT['start-text4'] = "Pour le support OpenSSL, veuillez utiliser le certificat de test avec <a href='https://127.0.0.1' target='_top'>https://127.0.0.1</a> ou <a href='https://localhost' target='_top'>https://localhost</a>";

	$TEXT['start-text5'] = "Et très important! Merci pour l'aide et le support de Carsten, Nemesis, KriS, Boppy, Pc-Dummy et tous les autres amis de XAMPP!";

	$TEXT['start-text6'] = "Bonne chance,<br>Kay Vogelgesang + Kai 'Oswald' Seidler";

	// ---------------------------------------------------------------------
	// MANUALS
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "Documentation en ligne";

	$TEXT['manuals-text1'] = "XAMPP regroupe beaucoup de logiciels différents dans un seul paquet. Voici une liste de documentation standard et de référence des paquetages les plus importants.";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/en/\">Documentation Apache 2</a>
	<li><a href=\"http://www.php.net/manual/en/\">Documentation de <b>référence </b>PHP</a>
	<li><a href=\"http://perldoc.perl.org/\">Documentation Perl </a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">Documentation MySQL</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccelerator</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">Documentation class fpdf </a>
	</ul>";

	$TEXT['manuals-text2'] = "Et une petite liste de tutoriaux et la page de documentation d'Apache Friends:";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/en/faq-xampp.html\">Documentation Apache Friends </a>
	<li><a href=\"http://www.freewebmasterhelp.com/tutorials/php/\"> Tutoriel PHP </a> by David Gowans
	<li><a href=\"http://www.davesite.com/webstation/html/\">HTML - Un Tutoriel Interactif pour Débutants</a> by Dave Kristula
	<li><a href=\"http://www.comp.leeds.ac.uk/Perl/start.html\"> Tutoriel Perl </a> by Nik Silver
	</ul>";

	$TEXT['manuals-text3'] = "Bonne Chance et Amusez vous bien! :)";

	// ---------------------------------------------------------------------
	// COMPONENTS
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "Composants XAMPP";

	$TEXT['components-text1'] = " XAMPP regroupe beaucoup de logiciels différents dans un seul paquet. Voici un aperçu de tous les paquetages.";

	$TEXT['components-text2'] = "Un grand merci aux développeurs de ces programmes.";

	$TEXT['components-text3'] = "Dans le répertoire <b>\\xampp\licenses</b> vous trouverez toutes les licences de ces programmes.";

	// ---------------------------------------------------------------------
	// CD COLLECTION DEMO
	// ---------------------------------------------------------------------

	$TEXT['cds-head'] = "CD Collection (Exemple pour les classes PHP+MySQL+PDF class)";
	$TEXT['cds-head-fpdf'] = "CD Collection (Exemple pour les classes PHP+MySQL+FPDF)";

	$TEXT['cds-text1'] = "Un programme de CD très simple.";

	$TEXT['cds-text2'] = "Liste de CD en temps que document <a href='$_SERVER[PHP_SELF]?action=getpdf'>PDF</a>.";

	$TEXT['cds-error'] = "Impossible d'accéder à la base de données!<br>Est-ce que MySQL est lancé, ou avez vous changé le mot de passe?";
	$TEXT['cds-head1'] = "Mes CDs";
	$TEXT['cds-attrib1'] = "Artiste";
	$TEXT['cds-attrib2'] = "Titre";
	$TEXT['cds-attrib3'] = "Année";
	$TEXT['cds-attrib4'] = "Commande";
	$TEXT['cds-sure'] = "Sur?";
	$TEXT['cds-head2'] = "Ajouter CD";
	$TEXT['cds-button1'] = "SUPPRIMER CD";
	$TEXT['cds-button2'] = "AJOUTER CD";

	// ---------------------------------------------------------------------
	// BIORHYTHM DEMO
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "Biorythme (Exemple pour PHP+GD)";

	$TEXT['bio-by'] = "par";
	$TEXT['bio-ask'] = "Veuillez entrer votre date de naissance";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "Date";
	$TEXT['bio-error2'] = "est invalide";

	$TEXT['bio-birthday'] = "Date de Naissance";
	$TEXT['bio-today'] = "Aujourd'hui";
	$TEXT['bio-intellectual'] = "Intellectuel";
	$TEXT['bio-emotional'] = "Emotionnel";
	$TEXT['bio-physical'] = "Physique";

	// ---------------------------------------------------------------------
	// INSTANT ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "Instant Art (Exemple pour PHP+GD+FreeType)";
	$TEXT['iart-text1'] = "Police \"AnkeCalligraph\" par <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// FLASH ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Flash Art (Exemple for PHP+MING)";
	$TEXT['flash-text1'] = "Police \"AnkeCalligraph\" par <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['flash-ok'] = "OK";

	// ---------------------------------------------------------------------
	// PHONE BOOK DEMO
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "Répertoire Téléphonique (Exemple pour PHP+SQLite)";

	$TEXT['phonebook-text1'] = "Un script très simple de répertoire téléphonique, mais implémenté avec une technologie moderne et récente: SQLite, la base de données SQL sans serveur.";

	$TEXT['phonebook-error'] = "Impossible d'ouvrir la base de données!";
	$TEXT['phonebook-head1'] = "Mes Numéros";
	$TEXT['phonebook-attrib1'] = "Nom";
	$TEXT['phonebook-attrib2'] = "Prénom";
	$TEXT['phonebook-attrib3'] = "Numéro";
	$TEXT['phonebook-attrib4'] = "Command";
	$TEXT['phonebook-sure'] = "Sur?";
	$TEXT['phonebook-head2'] = "Ajouter Entrée";
	$TEXT['phonebook-button1'] = "SUPPRIMER";
	$TEXT['phonebook-button2'] = "AJOUTER";

	// ---------------------------------------------------------------------
	// ABOUT
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "A Propos d'XAMPP";

	$TEXT['about-subhead1'] = "Idée et Réalisation";

	$TEXT['about-subhead2'] = "Design";

	$TEXT['about-subhead3'] = "Collaboration";

	$TEXT['about-subhead4'] = "Contacts";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "Mailing avec le serveur Mercury Mail SMTP et POP3";
	$TEXT['mail-hinweise'] = "Notes importantes sur l'utilisation de Mercury!";
	$TEXT['mail-adress'] = "Expéditeur:";
	$TEXT['mail-adressat'] = "Destinataire:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "Sujet:";
	$TEXT['mail-message'] = "Message:";
	$TEXT['mail-sendnow'] = "Envoi du message en cours...";
	$TEXT['mail-sendok'] = "Le message a été envoyé avec succès!";
	$TEXT['mail-sendnotok'] = "Erreur! L'envoi du message a échoué!";
	$TEXT['mail-help1'] = "Notes pour l'utilisation de Mercury:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>Mercury requiert une connexion externe au démarrage;</li>
	<li>au démarrage, Mercury défini le Service des Noms de Domaine (DNS) automatiquement en lui affectant le serveur de nom de votre provider;</li>
	<li>Pour les utilisateurs de serveurs Gateway: Veuillez spécifier votre DNS via TCP/IP;</li>
	<li>Le fichier de configuration de Mercury s'appelle MERCURY.INI;</li>
	<li>Pour tester, veuillez envoyer un message à postmaster@localhost ou admin@localhost et verifiez l'existance de ces messages dans les repertoires suivants : xampp.../mailserver/MAIL/postmaster ou (...)/admin;</li>
	<li>un utilisateur test nommé \"newuser\" (newuser@localhost) avec le mot de passe = wampp;</li>
	<li>le spam et autres obscénités sont strictement interdits avec Mercury!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "Serveur FTP FileZilla";
	$TEXT['filezilla-install'] = "Apache <U>n'est pas</U> un Serveur FTP... mais FileZilla FTP en est un. Veuillez considérer les références suivantes.";
	$TEXT['filezilla-install2'] = "Dans le répertoire principal de xampp, lancez \"filezilla_setup.bat\" pour le setup. Attention: Pour Windows NT, 2000 et XP Professionnel, FileZilla a besoin d'être installé en temps que service.";
	$TEXT['filezilla-install3'] = "Configurer \"FileZilla FTP\". Pour cela, veuillez utiliser l'interface FileZilla \"FileZilla Server Interface.exe\". Deux utilisateurs sont dans cet exemple:<br><br>
	A: Un utilisateur par défaut \"newuser\", password \"wampp\". Le répertoire racine est xampp\htdocs.<br>
	B: Un utilisateur anonymous \"anonymous\", pas de password. Le répertoire racine est xampp\anonymous.<br><br>
	L'adresse par defaut est l'adresse de loopback 127.0.0.1.";
	$TEXT['filezilla-install4'] = "Le serveur FTP s'arrête avec \"FileZillaFTP_stop.bat\". Pour le service FileZilla FTP, veuillez utiliser \"FileZillaServer.exe\". Apres, vous pouvez configurer les options de démarrage.";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "Export Excel avec PEAR (PHP)";
	$TEXT['pear-text'] = "Un petit <a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">Manuel</A> de Björn Schotte sur <a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A> (Allemand uniquement)";
	$TEXT['pear-cell'] = "La valeur d'une cellule";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Librairie graphique pour PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - Un autre accès BD (PHP)";
	$TEXT['ADOdb-text'] = "ADOdb signifie Active Data Objects Data Base. Nous supportons actuellement MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite et generic ODBC. Les drivers Sybase, Informix, FrontBase et PostgreSQL sont de contributions communautaires. Vous les trouverez ici \(mini)xampp\php\pear\adodb.";
	$TEXT['ADOdb-example'] = "L'exemple:";
	$TEXT['ADOdb-dbserver'] = "Serveur Base de Données (MySQL, Oracle ..?)";
	$TEXT['ADOdb-host'] = "Hôte dur serveur BD (nom ou IP)";
	$TEXT['ADOdb-user'] = "Username ";
	$TEXT['ADOdb-password'] = "Password";
	$TEXT['ADOdb-database'] = "Base de Données actuelle sur ce serveur";
	$TEXT['ADOdb-table'] = "Table sélectionnée de la base";
	$TEXT['ADOdb-nottable'] = "<p><b>Table introuvable!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>Le driver pour ce serveur de Base de Données n'existe pas ou peut-être qu'il s'agit du driver ODBC, ADO or OLEDB!</b>";


	// ONLY ENGLISH LANGUAGE SECTION

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
	$TEXT['switch-phpversion'] = "<i><b>Current in THIS XAMPP is ";
	$TEXT['switch-whatis'] = "<b>What make the PHP switch?</b><br>The apachefriends PHP Switch for XAMPP switching between the PHP version 4 to version 5 AND (!) back. So you can test your scripts with PHP 4 or PHP 5.<p>";
	$TEXT['switch-find'] = "<b>Where is the PHP Switch?</b><br>PHP Switch for XAMPP will execute a PHP file (XAMPP install folder) with the name \"php-switch.php\". You should use this batch file for executing: ";
	$TEXT['switch-care'] = "<b>What can be difficult?</b><br>PHP Switch will not change your PHP version, when a) the Apache HTTPD is running or/and b) the \".phpversion\" file in the install folder is vacant or have a bug. In the \".phpversion\", there was written the XAMPP current main PHP version number like \"4\" or \"5\". Please beginn with a \"shutdown\" for the Apache HTTPD and THEN execute the  \"php-switch.bat\".<p>";
	$TEXT['switch-where4'] = "<b>After That! Where are my (old) config files?</b><br><br>For PHP 4:<br>";
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
