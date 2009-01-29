<?php
	// ---------------------------------------------------------------------
	// GLOBAL
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP für Windows";
	$TEXT['global-showcode']="Quellcode zeigen";
	$TEXT['global-sourcecode']="Quellcode";

	// ---------------------------------------------------------------------
	// NAVIGATION
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "Willkommen";
	$TEXT['navi-status'] = "Status";
	$TEXT['navi-security'] = "Sicherheitscheck";
	$TEXT['navi-doc'] = "Dokumentation";
	$TEXT['navi-components'] = "Komponenten";
	$TEXT['navi-about'] = "Über XAMPP";

	$TEXT['navi-demos'] = "Demos";
	$TEXT['navi-cdcol'] = "CD-Verwaltung";
	$TEXT['navi-bio'] = "Biorhythmus";
	$TEXT['navi-guest'] = "Gästebuch";
	$TEXT['navi-perlenv'] = "MiniPerl";
	$TEXT['navi-iart'] = "Instant Art";
	$TEXT['navi-iart2'] = "Flash Art";
	$TEXT['navi-phonebook'] = "Telefonbuch";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Writer";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Perl";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "PHP Umschalter";

	$TEXT['navi-tools'] = "Tools";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Current Guest";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "Sprachen";

	// ---------------------------------------------------------------------
	// STATUS
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "XAMPP-Status";
	$TEXT['status-text1'] = "Auf dieser Übersicht kann man sehen welche XAMPP-Komponenten gestartet sind bzw. welche funktionieren. Sofern nichts an der Konfiguration von XAMPP geändert wurde, sollten MySQL, PHP, Perl, CGI und SSI aktiviert sein.";
	$TEXT['status-text2'] = "Dieser Check funktioniert nur zuverlässig solange nichts an der Konfiguration des Apache geändert wurde. Durch bestimmte Änderungen kann das Ergebnis dieses Tests verfälscht werden. Mit SSL (https://localhost) funktionieren die Statuschecks nicht!";

	$TEXT['status-mysql'] = "MySQL-Datenbank";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perl mit mod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-python'] = "Python mit mod_python";
	$TEXT['status-smtp'] = "SMTP Server";
	$TEXT['status-ftp'] = "FTP Server";
	$TEXT['status-tomcat'] = "Tomcat Server";
	$TEXT['status-named'] = "Domain Name Server (DNS)";
	$TEXT['status-mmcache'] = "PHP-Erweiterung »Turck MMCache«";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-wampp.html#mmcache";
	$TEXT['status-oci8'] = "PHP-Erweiterung »OCI8/Oracle«";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp.html#oci8";

	$TEXT['status-lookfaq'] = "siehe FAQ";
	$TEXT['status-ok'] = "AKTIVIERT";
	$TEXT['status-nok'] = "DEAKTIVIERT";

	$TEXT['status-tab1'] = "Komponente";
	$TEXT['status-tab2'] = "Status";
	$TEXT['status-tab3'] = "Hinweis";

	// ---------------------------------------------------------------------
	// SECURITY
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "XAMPP SICHERHEIT";
	$TEXT['security-text1'] = "Anhand dieser Übersicht kann man sehen welche Punkte an der XAMPP-Installation noch unsicher sind und noch überprüft werden müssten. (Bitte unter der Tabelle weiterlesen.)";
	$TEXT['security-text2'] = "Die grün markierten Punkte sind sicher; die rot marktierten Punkte sind definitiv unsicher und bei den gelb martierten Punkten konnte die Sicherheit nicht überprüft werden (zum Beispiel weil das zu testende Programm gar nicht läuft).<br>&nbsp;<br><b>Diese Sicherheitslücken im XAMPP Verzeichnis, dem ROOT Passwort für MySQL und der Sicherung von PHPMyAdmin können nun einfach über den folgenden Link geschlossen werden:</b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[nur über localhost möglich]<br>&nbsp;<br>&nbsp;<br>
	Weitere wichtige Hinweise:<ul>
	<li>Alle XAMPP Prüfungen beziehen sich nur auf den Host \"localhost\" (127.0.0.1).</li>
	<li><i><b>Der FileZilla FTP und der Mercury Mail Server müsst ihr selbst absichern !!</b></i></li>
	<li>Ein Rechner, der nicht von aussen via Internet oder Intranet angreifbar ist<br>(z.B. weil eine Firewall alle wichtigen Ports nach aussen blockt) ist generell SICHER gegen Angriffe von aussen.</li>
	<li>Ein Server, der gar nicht \"an ist\" und damit läuft ist immer SICHER!</li></ul>";
	$TEXT['security-text3'] = "<b>Bitte unbedingt beachten: Je mehr Sicherheit der XAMPP hat, desto weniger Beispiele werden fehlerfrei angezeigt. Wer beispielsweise PHP im \"safe mode\" fährt, wird nicht mehr vollständig auf alle Funktionen dieses Security Frondends zugreifen können. Oft bedeutet mehr Sicherheit gleichzeitig weniger Funktionalität.</b>";
	$TEXT['security-text4'] = "Die XAMPP Standard Ports:";

	$TEXT['security-ok'] = "SICHER";
	$TEXT['security-nok'] = "UNSICHER";
	$TEXT['security-noidea'] = "UNBEKANNT";

	$TEXT['security-tab1'] = "Betreff";
	$TEXT['security-tab2'] = "Status";

	$TEXT['security-checkapache-nok'] = "Diese XAMPP-Seiten sind über's Netzwerk erreichbar";
	$TEXT['security-checkapache-ok'] = "Diese XAMPP-Seiten sind nicht über's Netzwerk erreichbar";
	$TEXT['security-checkapache-text'] = "Alles was Du hier sehen kannst, kann potentiell auch jeder Aussenstehender sehen und nutzen, der Deinen Rechner über's Netzwerk erreichen kann. Wenn Du zum Beispiel mit diesem Rechner ins Internet geht, dann kann jeder im Internet, der Deine IP-Adresse kennt oder rät auf diese Seiten zugreifen.";

	$TEXT['security-checkmysqlport-nok'] = "MySQL ist über's Netzwerk erreichbar";
	$TEXT['security-checkmysqlport-ok'] = "MySQL ist nicht über's Netzwerk erreichbar";
	$TEXT['security-checkmysqlport-text'] = "Auf die MySQL-Datenbank kann potentiell über's Netzwerk zugegriffen werden. Zwar ist es in der Standardinstallation von XAMPP trotzdem nicht möglich von außen Zugriff zur Datenbank zu bekommen. Aber um eine absolute Sicherheit zu bekommen sollte der Netzwerkzugriff auf MySQL abgeschaltet werden.";

	$TEXT['security-checkpmamysqluser-nok'] = "Der phpMyAdmin-Benutzer pma hat kein Passwort";
	$TEXT['security-checkpmamysqluser-ok'] = "Der phpMyAdmin-Benutzer hat ein Passwort";
	$TEXT['security-checkpmamysqluser-text'] = "phpMyAdmin speichtert seine eigenen Einstellungen in der MySQL-Datenbank. phpMyAdmin benutzt dazu den MySQL-Benutzer pma. Damit sonst niemand anderes als phpMyAdmin über diesen Benutzer auf die Datenbank zugreifen kann, sollte diesem Benutzer ein Passwort gesetzt werden.";

	$TEXT['security-checkmysql-nok'] = "MySQL Admin User \"root\" hat kein Passwort";
	$TEXT['security-checkmysql-ok'] = "MySQL Admin User \"root\" hat ein Passwort";
	$TEXT['security-checkmysql-out'] = " Ein MySQL Server läuft nicht oder wird von einer Firewall geblockt!";
	$TEXT['security-checkmysql-text'] = "Der MySQL Admin User \"root\" hat noch kein Passwort gesetzt bekommen. Jeder Benutzer auf dem Rechner kann so auf der MySQL-Datenbank machen was er will. Der MySQL-root sollte also auf alle Fälle ein Passwort gesetzt bekommen.";

	$TEXT['security-pop-nok'] = "Der Testuser (newuser) für den Mercury Mail Server (POP3) hat ein altes Passwort (wampp)";
	$TEXT['security-pop-ok'] = "Der Testuser \"newuser\" für den POP3 Server (Mercury Mail?) existiert nicht mehr oder hat ein neues Passwort";
	$TEXT['security-pop-out'] = "Ein POP3 Server wie Mercury Mail läuft nicht oder wird von einer Firewall geblockt!";
	$TEXT['security-pop-notload'] = "<i>Die für eine Prüfung notwendige IMAP Extension (php.ini) ist nicht geladen!</i><br>";
	$TEXT['security-pop-text'] = "Bitte UNBEDINGT den Mercury Mail Server auf alle User mit deren Passwörtern überprüfen und ggf. auch verändern!";

	$TEXT['security-checkftppassword-nok'] = "Das FileZilla FTP-Passwort ist noch immer 'wampp'";
	$TEXT['security-checkftppassword-ok'] = "Das FileZilla FTP-Passwort wurde geändert";
	$TEXT['security-checkftppassword-out'] = "Ein FTP Server läuft nicht  oder wird von einer Firewall geblockt!";
	$TEXT['security-checkftppassword-text'] = "Wenn Du den FileZilla FTP Server gestartet hast, kannst Du standardmäßig mit dem Benutzernamen 'newuser' und dem Passwort 'wampp' Dateien für Deinen Webserver hochladen. Potentiell kann das natürlich jeder und daher sollte hier unbeding ein anderes Passwort gesetzt werden.";

	$TEXT['security-phpmyadmin-nok'] = "PhpMyAdmin ist über das Netzwerk erreichbar";
	$TEXT['security-phpmyadmin-ok'] = "PhpMyAdmin Passwort Schutz wurde aktiviert";
	$TEXT['security-phpmyadmin-out'] = "PhpMyAdmin: Kann die 'config.inc.php' NICHT finden ...";
	$TEXT['security-phpmyadmin-text'] = "PhpMyAdmin ist ohne Passwort über das Netz erreichbar. Die Einstellung 'httpd' oder 'cookie' in der config.inc.php kann hier abhelfen.";

	$TEXT['security-checkphp-nok'] = "PHP läuft NICHT im \"Safe Mode\"";
	$TEXT['security-checkphp-ok'] = "PHP läuft im \"Safe Mode\"";
	$TEXT['security-checkphp-out'] = "Der PHP Status kann nicht kontrolliert werden!";
	$TEXT['security-checkphp-text'] = "Wer auf seinem Server die Ausführung von PHP auch für Aussenstehende zuläßt, sollte sich aus Gründen der Sicherheit überlegen, ob er PHP im sog. \"Safe Mode\" konfiguriert. Für reine Entwickler ist allerdings der \"Safe Mode\" nicht zu empfehlen, da manche Funktionen eingeschränkt oder überhaupt nicht mehr ausgeführt werden. <a href=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>Mehr Info</font></a>";

	// ---------------------------------------------------------------------
	// SECURITY SETUP
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "Security Konsole MySQL | XAMPP Verzeichnis Schutz";
	$TEXT['mysql-rootsetup-head'] = "MYSQL SEKTION: \"ROOT\" PASSWORT";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "Der MySQL Server ist nicht gestartet oder wird von einer Firewall geblockt! Bitte zuerst dieses Problem überprüfen ...";
	$TEXT['mysql-rootsetup-passwdnotok'] = "Das neuen Passwort war mit der Wiederholung des neuen Passworts nicht identisch! Bitte geben Sie das neue Passwort sowie dessen Wiederholung erneut ein!";
	$TEXT['mysql-rootsetup-passwdnull'] = "Null-Passwörter ('leer') werden nicht akzeptiert!";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "ERFOLG: Das Passwort für den administrativen Benutzer 'root' wurde gesetzt oder aktualisiert! Die Initialisierung des neuen Passwortes erfolgt aber erst nach einem NEUSTART VON MYSQL !!!! Die Daten mit dem neuen Passwort wurde zur Sicherheit in folgende Datei archiviert:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "FEHLER: ROOT Passwort ist vermutlich falsch. MySQL lehnte das Login mit dem aktuellen root-Passwort ab.";
	$TEXT['mysql-rootsetup-passwdold'] = "Akuelles Passwort:";
	$TEXT['mysql-rootsetup-passwd'] = "Neues Passwort:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "Wiederhole neues Passwort:";
	$TEXT['mysql-rootsetup-passwdchange'] = "Passwort ändern";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "PhpMyAdmin Authentifikation:";

	$TEXT['xampp-setup-head'] = "XAMPP VERZEICHNIS SCHUTZ (.htaccess)";
	$TEXT['xampp-setup-user'] = "Benutzer (User):";
	$TEXT['xampp-setup-passwd'] = "Passwort:";
	$TEXT['xampp-setup-start'] = "XAMPP Verzeichnis sichern";
	$TEXT['xampp-setup-notok'] = "<br><br>FEHLER: Der String für den Benutzer sowie für das Passwort muss mindestens 3 und darf höchstens 15 Zeichen haben, und darf KEINE Sonderzeichen wie <öäü (usw.) als auch Leerzeichen enthalten.<br><br>";
	$TEXT['xampp-setup-ok'] = "<br><br>Das root Passwort wurde erfolgreich geändert. Bitte starten Sie den MySQL Server neu, damit diese Änderung wirksam wird!<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>ERFOLG: Das XAMPP Verzeichnis ist nach nun geschützt. Die Daten wurden in folgenden Dateien archiviert:<br>";
	$TEXT['xampp-config-notok'] = "<br><br>FEHLER: Das System konnte NICHT mit Hilfe der \".htaccess\" und der \"htpasswd.exe\" den Verzeichnisschutz aktivieren bzw. aktualisieren. Unter Umständen ist PHP im \"Safe Modus\".   <br><br>";

	// ---------------------------------------------------------------------
	// START
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "Willkommen zu XAMPP für Windows";

	$TEXT['start-subhead'] = "Herzlichen Glückwunsch:<br>XAMPP ist erfolgreich auf diesem Rechner installiert!";

	$TEXT['start-text1'] = "Nun kann es losgehen. :) Als erstes bitte einmal auf der linken Seite auf »Status« klicken. Damit bekommt man einen Überblick was alles schon funktioniert. Ein paar Funktionen werden ausgeschaltet sein. Das ist Absicht so. Es sind Funktionen, die nicht überall funktionieren oder evtl. Probleme bereiten könnten.";

	$TEXT['start-text2'] = "";

	$TEXT['start-text3'] = "";

	$TEXT['start-text4'] = "Für die OpenSSL Unterstützung benutzt bitte das Testzertifikat mit der URL <a href='https://127.0.0.1' target='_top'>https://127.0.0.1</a> bzw. <a href='https://localhost' target='_top'>https://localhost</a>";

	$TEXT['start-text5'] = "In dieser Release geht ein herzlicher Dank an <a href=\"http://www.php.net/credits.php\" target=\"_new\">Uwe Steinmann</a> für seine ausgezeichnete Entwicklung und Übersetzung der aktuellen \"Special\" Module!";

	$TEXT['start-text6'] = "Viel Spaß, Kay Vogelgesang + Kai 'Oswald' Seidler";

	// ---------------------------------------------------------------------
	// MANUALS
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "Online-Dokumentation";

	$TEXT['manuals-text1'] = "XAMPP verbindet viele unterschiedliche Pakete in einem Paket. Hier ist eine Auswahl der Standard- und Referenz-Dokumentationen zu den wichtigsten Paketen von XAMPP.";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/de/\">Apache 2 Dokumentation (in Deutsch)</a>
	<li><a href=\"http://www.php.net/manual/de/\">PHP <b>Referenz-</b>Dokumentation (in Deutsch)</a>
	<li><a href=\"http://perldoc.perl.org/\">Perl Dokumentation (in Englisch)</a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">MySQL Dokumentation (in Englisch)</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB (in Englisch)</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccelerator (in Englisch)</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">FPDF Class Dokumentation (in Englisch)</a>
	</ul>";

	$TEXT['manuals-text2'] = "Und hier noch eine kleine Auswahl an deutschsprachigen Anleitungen und die zentrale Dokumentations-Seite von Apache Friends:";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/de/faq-xampp.html\">Apache Friends Dokumentation</a>
	<li><a href=\"http://www.schattenbaum.net/php/\">PHP für Dich</a> (incl. MySQL-Einführung) von Claudia Schaffarik
	<li><a href=\"http://de.selfhtml.org/\">SELFHTML</a> von Stefan Münz
	<li><a href=\"http://www.stephan-muller.com/cgi/\">CGI Einführung</a> von Stephan Muller
	</ul>";

	$TEXT['manuals-text3'] = "Viel Spaß und Erfolg beim Lesen! :)";

	// ---------------------------------------------------------------------
	// COMPONENTS
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "XAMPP-Komponenten";

	$TEXT['components-text1'] = "XAMPP verbindet viele unterschiedliche Pakete in einem Paket. Hier ist eine Übersicht aller enthaltenen Pakete.";

	$TEXT['components-text2'] = "Vielen Dank an die unzähligen Autoren dieser Programme.";

	$TEXT['components-text3'] = "Im Verzeichnis <b>\\xampp\licenses</b> befinden sich die einzelnen Lizenz-Texte dieser Pakete.";

	// ---------------------------------------------------------------------
	// CD COLLECTION DEMO
	// ---------------------------------------------------------------------

    $TEXT['cds-head']="CD-Verwaltung (Beispiel für PHP+MySQL+PDF Class)";
    $TEXT['cds-head-fpdf']="CD-Verwaltung (Beispiel für PHP+MySQL+FPDF)";

	$TEXT['cds-text1'] = "Eine sehr einfach CD-Verwaltung. Da man Eintäge nicht mehr verbessern kann, wenn man sich mal vertippt hat, empfiehlt sich phpMyAdmin (unten links in der Navigation).";

	$TEXT['cds-text2'] = "<b>Neu seit 0.9.6:</b> Ausgabe der eingestellten CDs als <a href='$_SERVER[PHP_SELF]?action=getpdf'>PDF-Dokument</a>.";

	$TEXT['cds-error'] = "Kann die Datenbank nicht erreichen!<br>Läuft MySQL oder wurde das Passwort geändert?";
	$TEXT['cds-head1'] = "Meine CDs";
	$TEXT['cds-attrib1'] = "Interpret";
	$TEXT['cds-attrib2'] = "Titel";
	$TEXT['cds-attrib3'] = "Jahr";
	$TEXT['cds-attrib4'] = "Aktion";
	$TEXT['cds-sure'] = "Wirklich sicher?";
	$TEXT['cds-head2'] = "CD hinzufügen";
	$TEXT['cds-button1'] = "CD LÖSCHEN";
	$TEXT['cds-button2'] = "CD HINZUFÜGEN";

	// ---------------------------------------------------------------------
	// BIORHYTHM DEMO
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "Biorhythm (mit PHP+GD)";
	$TEXT['bio-head'] = "Biorhythmus (Beispiel für PHP+GD)";

	$TEXT['bio-by'] = "von";
	$TEXT['bio-ask'] = "Bitte gib dein Geburtsdatum ein";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "Das Datum";
	$TEXT['bio-error2'] = "ist ungültig";

	$TEXT['bio-birthday'] = "Geburtstag";
	$TEXT['bio-today'] = "Heute";
	$TEXT['bio-intellectual'] = "Intelligenz";
	$TEXT['bio-emotional'] = "Emotion";
	$TEXT['bio-physical'] = "Körper";

	// ---------------------------------------------------------------------
	// INSTANT ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "Instant Art (Beispiel für PHP+GD+FreeType)";
	$TEXT['iart-text1'] = "Font »AnkeCalligraph« von <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// FLASH ART DEMO
	// ---------------------------------------------------------------------

	// $TEXT['flash-head'] = "Flash Art (Beispiel für PHP+MING)";
	// $TEXT['flash-text1'] = "Das MING Projekt für Windows wurde leider nicht weiterverfolgt und ist deshalb unvollständig.<br>Vgl. bitte <a class=blue target=extern href=\"http://ming.sourceforge.net/install.html/\">Ming - an SWF output library and PHP module</a>";
	// $TEXT['flash-ok'] = "OK";


	// ---------------------------------------------------------------------
	// FLASH ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Flash Art (Beispiel für PHP+MING)";
	$TEXT['flash-text2'] = "Font »AnkeCalligraph« von <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['flash-ok'] = "OK";
	$TEXT['flash-text1'] = "Das MING Projekt für Windows wurde leider nicht weiterverfolgt und ist deshalb unvollständig.<br>Vgl. bitte <a class=blue target=extern href=\"http://www.opaque.net/wiki/index.php?Ming\">Ming - an SWF output library and PHP module</a>";

	// ---------------------------------------------------------------------
	// PHONE BOOK DEMO
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "Telefonbuch (Beispiel für PHP+SQLite)";

	$TEXT['phonebook-text1'] = "Ein sehr einfaches Telefonbuch. Allerdings mit einer sehr aktuellen Technik:<br>SQLite, einer SQL-Datenbank ohne extra Server.";

	$TEXT['phonebook-error'] = "Kann die Datenbank nicht öffnen!";
	$TEXT['phonebook-head1'] = "Meine Telefonnummern";
	$TEXT['phonebook-attrib1'] = "Nachname";
	$TEXT['phonebook-attrib2'] = "Vorname";
	$TEXT['phonebook-attrib3'] = "Telefonnummer";
	$TEXT['phonebook-attrib4'] = "Aktion";
	$TEXT['phonebook-sure'] = "Wirklich sicher?";
	$TEXT['phonebook-head2'] = "Eintrag hinzufügen";
	$TEXT['phonebook-button1'] = "LÖSCHEN";
	$TEXT['phonebook-button2'] = "HINZUFÜGEN";

	// ---------------------------------------------------------------------
	// ABOUT
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "Über XAMPP";

	$TEXT['about-subhead1'] = "Konzept und Umsetzung";

	$TEXT['about-subhead2'] = "Design";

	$TEXT['about-subhead3'] = "Mitwirkung";

	$TEXT['about-subhead4'] = "Ansprechpartner";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "Mailing mit Mercury Mail SMTP und POP3 Server";
	$TEXT['mail-hinweise'] = "Einige wichtige Hinweise zur Nutzung von Mercury findet ihr hier!";
	$TEXT['mail-adress'] = "Adresse des Absenders:";
	$TEXT['mail-adressat'] = "Adresse des Adressats:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "Betreff:";
	$TEXT['mail-message'] = "Nachricht:";
	$TEXT['mail-sendnow'] = "Die Nachricht wird nun versendet ...";
	$TEXT['mail-sendok'] = "Die Nachricht wurde erfolgreich versandt!";
	$TEXT['mail-sendnotok'] = "Fehler! Die Nachricht konnte nicht versendet werden!";
	$TEXT['mail-help1'] = "Hinweise für die Nutzung von Mercury:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>Mercury braucht beim Start eine Aussenbindung (DFÜ oder DSL);</li>
	<li>Beim start setzt Mercury seinen Domain Name Service (DNS) automatisch auf den Nameserver des Providers;</li>
	<li>Benutzer von Gateway-Servern sollten hingegen einen Domain Name Server via TCP/IP gesetzt haben (z.B. von T-Online mit der IP 194.25.2.129);</li>
	<li>Die Config-Datei von Mercury lautet MERCURY.INI;</li>
	<li>Lokal versandte E-Mails werden u.U. von manchen Providern abgelehnt (vorallem von T-Online und AOL). Der Grund: Diese Provider überprüfen den Mail Header bezüglich einer RELAY Option, um SPAM zu vermeiden;</li>
	<li>lokal zum Testen eine E-Mail an die User postmaster@localhost und admin@localhost senden und den Eingang in den Verzeichnissen xampp.../mailserver/MAIL/postmaster und (...)/admin kontrollieren;</li>
	<li>ein Test Benutzer heißt \"newuser\" (newuser@localhost) mit dem Passwort = wampp;</li>
	<li>SPAM und andere Schweinereien sind mit Mercury total verboten!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "FileZilla FTP Server";
	$TEXT['filezilla-install'] = "Der Apache ist <U>kein</U> FTP Server ... aber FileZilla FTP ist einer. Bitte beachtet die folgenden Hinweise.";
	$TEXT['filezilla-install2'] = "Einfach die Datei \"filezilla_setup.bat\" im Hauptverzeichnis des xampp starten, um den FTP Server einzurichten. Unter Windows NT, 2000 und XP Professional Betrienssystemen wird der Nutzer nun automatisch aufgefordert, FileZilla als Dienst zu installieren, damit der Server starten kann.";
	$TEXT['filezilla-install3'] = "Nun könnt ihr \"FileZilla FTP\" konfigurieren. Nutzt dazu das FileZilla Interface namens \"FileZilla Server Interface.exe\" im FileZilla-Verzeichnis. Natürlich könnt ihr euch an der Beispielkonfiguration orientieren. Zwei Nutzer wurde in dem Beispiel angelegt:<br><br>
	A: Ein Standardnutzer namens \"newuser\", Kennwort \"wampp\". Das Heimatverzeichnis ist xampp\htdocs.<br>
	B: Ein Anonymous User namens \"anonymous\", kein Kennwort. Das Heimatverzeichnis ist xampp\anonymous. Kann via Browser mit <a href=\"ftp://127.0.0.1\" target=\"_new\">ftp://127.0.0.1</a> angesprochen werden.<br><br>Der FileZilla ist hier erst einmal nur über die Loopback Adresse 127.0.0.1 gebunden, ihr könnt den zu nutzenden IP Addressbereich aber noch über das FileZilla Interface ändern.";
	$TEXT['filezilla-install4'] = "Den FTP Server stoppen mit \"FileZillaFTP_stop.bat\". Wer den Server als Dienst starten möchte, sollte die Exekute-Datei mal mit Doppelklick auf \"FileZillaServer.exe\" starten. Dieser fragt dann nach den ganzen Startoptionen.";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "Excel Export mit PEAR (PHP)";
	$TEXT['pear-text'] = "Ein kurzes <a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">Manual</A> machte freundlicher Weise Björn Schotte von <a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A>";
	$TEXT['pear-cell'] = "Der Inhalt einer Excel Zelle";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Grafik Bibliotheken für PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - Der andere Datenbank-Zugriff (PHP)";
	$TEXT['ADOdb-text'] = "ADOdb steht für Active Data Objects Data Base und unterstützt MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite sowie ODBC. Die Sybase, Informix, FrontBase und PostgreSQL Treiber sind Gemeinschaftsbeiträge. Ihr findet es hier unter \(mini)xampp\php\pear\adodb.";
	$TEXT['ADOdb-example'] = "Das Beispiel:";
	$TEXT['ADOdb-dbserver'] = "Datenbankserver (MySQL, Oracle ..?)";
	$TEXT['ADOdb-host'] = "Host des DB-Servers (Name oder IP)";
	$TEXT['ADOdb-user'] = "Name des zugriffsberechtigten Nutzers";
	$TEXT['ADOdb-password'] = "Passwort des zugriffsberechtigten Nutzers";
	$TEXT['ADOdb-database'] = "Datenbank auf dem Datenbankserver";
	$TEXT['ADOdb-table'] = "Tabelle dieser Datenbank";
	$TEXT['ADOdb-nottable'] = "<p><b>Tabelle nicht gefunden!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>Der Treiber für diesen Datenbankserver existiert nicht oder es handelt es ich um ein ODBC, ADO oder OLEDB Treiber!</b>";

	// ---------------------------------------------------------------------
	// INFO
	// ---------------------------------------------------------------------

	$TEXT['info-package'] = "Packet";
	$TEXT['info-pages'] = "Seiten";
	$TEXT['info-extension'] = "Endungen";
	$TEXT['info-module'] = "Apache Modul";
	$TEXT['info-description'] = "Beschreibung";
	$TEXT['info-signature'] = "Signatur";
	$TEXT['info-docdir'] = "Dokumentverzeichnis";
	$TEXT['info-port'] = "Standard Port";
	$TEXT['info-service'] = "Dienste";
	$TEXT['info-examples'] = "Beispiele";
	$TEXT['info-conf'] = "Konfigurationsdateien";
	$TEXT['info-requires'] = "Braucht";
	$TEXT['info-alternative'] = "Alternativ";
	$TEXT['info-tomcatwarn'] = "Warnung! Tomcat ist nicht auf Port 8080 gestartet.";
	$TEXT['info-tomcatok'] = "OK! Der Tomcat ist auf Port 8080 erfolgreich gestartet.";
	$TEXT['info-tryjava'] = "Das Java Beispiel (JSP) über Apache MOD_JK.";
	$TEXT['info-nococoon'] = "Warnung! Tomcat ist nicht auf Port 8080 gestartet. So kann ich nicht \"Cocoon\" installieren!";
	$TEXT['info-okcocoon'] = "Ok! Der Tomcat ist hochgefahren. Die Installation kann ein paar Minuten dauern. Zum installieren von \"Cocoon\" klicke nun hier ...";

	// ---------------------------------------------------------------------
	// PHP Switch
	// ---------------------------------------------------------------------

	$TEXT['switch-head'] = "PHP Switch 1.0 win32 für XAMPP";
	$TEXT['switch-phpversion'] = "<i><b>Aktuell in diesem XAMPP ist ";
	$TEXT['switch-whatis'] = "<b>Was macht eigentlich der PHP Switch?</b><br>Der ApacheFriends PHP Switch für den XAMPP wechselt zwischen der PHP Version 4 zu der Version 5 UND zurück. Damit kannst du deine Skripte mit PHP 4 oder PHP 5 testen.<p>";
	$TEXT['switch-find'] = "<b>Wo ist der PHP Switch?</b><br>PHP Switch für den XAMPP ist eine PHP Datei im XAMPP install Ordner mit dem Namen \"php-switch.php\" ausführt. Ausgeführt wird der Wechsel mit dem Batchfile ";
	$TEXT['switch-care'] = "<b>Was muss ich beachten?</b><br>PHP Switch weigert sich den Wechsel vorzunehmen, wenn a) der Apache noch läuft und b) \".phpversion\" Datei im install Ordner fehlt oder fehlerhaft ist. In der \".phpversion\" steht die (Haupt) Nummer der gerade benutzen PHP Version. Also zu Beginn \"shutdown\" Apache und erst dann die  \"php-switch.bat\" ausführen.<p>";
	$TEXT['switch-where4'] = "<b>Wo sind danach meine (alten) Konfigurationsdateien?</b><br><br>Für PHP 4:<br>";
	$TEXT['switch-where5'] = "<br><br>Für PHP 5:<br>";
	$TEXT['switch-make1'] = "<b>Werden denn Änderungen übernommen?</b><br><br>Ja! Für PHP4 oder PHP5 jeweils in der<br>";
	$TEXT['switch-make2'] = "<br><br> .. gesichert bei PHP4 ...<br>";
	$TEXT['switch-make3'] = "<br><br> .. gesichert bei PHP5 ...<br>";
	$TEXT['switch-make4'] = "<br><br>Und auch wieder bei einem \"switch\" zurückgeführt!!<p>";
	$TEXT['switch-not'] = "<b>Ich bin so zufrieden und möchte keinen \"Switch\" !!!</b><br>Super! Dann vergiß das ganze  hier ... ;-)<br>";

	// ---------------------------------------------------------------------
	// Cocoon
	// ---------------------------------------------------------------------

	$TEXT['go-cocoon'] = "Cocoon nun über http://localhost/cocoon/ aufrufen!";
	$TEXT['path-cocoon'] = "Der Verzeichnispfad zu Cocoon lautet: ...\\xampp\\tomcat\\webapps\\cocoon";

	// ---------------------------------------------------------------------
	// Guest
	// ---------------------------------------------------------------------

	$TEXT['guest1-name'] = "Current Guest in dieser Release: <i>FCKeditor</i>";
	$TEXT['guest1-text1'] = "Ein netter HMTL ONLINE Editor mit viel JavaScript. Optimiert für den IE, funktioniert übrigens nicht mit dem Mozilla FireFox.";
	$TEXT['guest1-text2'] = "FCKeditor Homepage: <a href=\"http://www.fckeditor.net\" target=\"_new\">www.fckeditor.net</a>. Übrigens: Der Font Arial funktioniert nicht, aber vielleicht weiß hier jemand weiter?";
	$TEXT['guest1-text3'] = "<a href=\"guest-FCKeditor/fckedit-dynpage.php\" target=\"_new\">Zur dynamischen Seite bearbeitet mit FCKeditor.</A>";
	
	// ---------------------------------------------------------------------
	// NAVI SPECIALS SECTION
	// ---------------------------------------------------------------------	
	
	$TEXT['navi-specials'] = "Specials";
	
  	// ---------------------------------------------------------------------
	// PS AND PARADOX EXAMPLE
	// ---------------------------------------------------------------------

  	$TEXT['navi-ps'] = "PHP PostScript";
	$TEXT['ps-head'] = "PostScript Modul Beispiel";
	$TEXT['ps-text1'] = "PostScript Modul »php_ps« von <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['ps-text2'] = "Tipp: Zum konvertieren von PS-Dateien zu PDF-Dateien unter win32 kann <a href=\"http://www.shbox.de/\" target=\"_new\">FreePDF</a> mit <a href=\"http://www.ghostscript.com/awki/\" target=\"_new\">GhostScript</a> benutzt werden.";
  
  	$TEXT['navi-paradox'] = "PHP Paradox";
  	$TEXT['paradox-head'] = "Paradox Modul Beispiel";
	$TEXT['paradox-text1'] = "Paradox Modul »php_paradox« von <a class=blue target=extern href=\"mailto:steinm@php.net\">Uwe Steinmann</a>";
	$TEXT['paradox-text2'] = "<h2>Lesen und Schreiben einer Paradax Datenbank</h2>";
	$TEXT['paradox-text3'] = "Weitere Beispiele gibt es im Verzeichnis ";
  	$TEXT['paradox-text4'] = "Weiterführende Informationen zu Paradox Datenbanken in <a href=\"http://de.wikipedia.org/wiki/Paradox_%28Datenbank%29\" target=\"_new\">WikiPedia</a>.";
?>
