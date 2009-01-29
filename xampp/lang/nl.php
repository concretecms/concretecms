<?php
	// ---------------------------------------------------------------------
	// GLOBAL
	// ---------------------------------------------------------------------

	$TEXT['global-xampp'] = "XAMPP for Windows";
	$TEXT['global-showcode'] = "Toon de broncode";
	$TEXT['global-sourcecode'] = "Broncode";

	// ---------------------------------------------------------------------
	// NAVIGATION
	// ---------------------------------------------------------------------

	$TEXT['navi-xampp'] = "XAMPP";
	$TEXT['navi-welcome'] = "Welkom";
	$TEXT['navi-status'] = "Status";
	$TEXT['navi-security'] = "Beveiliging";
	$TEXT['navi-doc'] = "Documentatie";
	$TEXT['navi-components'] = "Componenten";
	$TEXT['navi-about'] = "Over XAMPP";
	$TEXT['navi-security'] = "Veiligheid";


	$TEXT['navi-demos'] = "Demo's";
	$TEXT['navi-cdcol'] = "CD-Collectie";
	$TEXT['navi-bio'] = "Bioritme";
	$TEXT['navi-guest'] = "GastenBoek";
	$TEXT['navi-iart'] = "Instant Art";
	$TEXT['navi-iart2'] = "Flash Art";
	$TEXT['navi-phonebook'] = "TelefoonBoek";
	$TEXT['navi-perlasp'] = "Perl:ASP";
	$TEXT['navi-pear'] = "Excel_Writer";
	$TEXT['navi-adodb'] = "ADOdb";
	$TEXT['navi-perl'] = "Perl";
	$TEXT['navi-python'] = "Python";
	$TEXT['navi-jsp'] = "Java";
	$TEXT['navi-phpswitch'] = "PHP Switch";

	$TEXT['navi-tools'] = "Gereedschappen";
	$TEXT['navi-phpmyadmin'] = "phpMyAdmin";
	$TEXT['navi-webalizer'] = "Webalizer";
	$TEXT['navi-phpsqliteadmin'] = "phpSQLiteAdmin";
	$TEXT['navi-mercury'] = "Mercury Mail";
	$TEXT['navi-filezilla'] = "FileZilla FTP";
	$TEXT['navi-jpgraph'] = "JpGraph";

	$TEXT['navi-specialguest'] = "Huidige Bezoeker";
	$TEXT['navi-guest1'] = "FCKeditor";

	$TEXT['navi-languages'] = "Talen";

	// ---------------------------------------------------------------------
	// STATUS
	// ---------------------------------------------------------------------

	$TEXT['status-head'] = "XAMPP Status";
	$TEXT['status-text1'] = "Deze bladzijde toont in een oogopslag alle informatie. Wat draait er, wat werkt en wat niet.";
	$TEXT['status-text2'] = "NB: Veranderingen in de configuratie veroorzaken soms foutieve statusmeldingen.";

	$TEXT['status-mysql'] = "MySQL database";
	$TEXT['status-ssl'] = "HTTPS (SSL)";
	$TEXT['status-php'] = "PHP";
	$TEXT['status-perl'] = "Perl met mod_perl";
	$TEXT['status-cgi'] = "Common Gateway Interface (CGI)";
	$TEXT['status-ssi'] = "Server Side Includes (SSI)";
	$TEXT['status-python'] = "Python met mod_python";
	$TEXT['status-mmcache'] = "PHP extensie »Turck MMCache«";
	$TEXT['status-mmcache-url'] = "http://www.apachefriends.org/faq-wampp-en.html#mmcache";
	$TEXT['status-smtp'] = "SMTP Service";
	$TEXT['status-ftp'] = "FTP Service";
	$TEXT['status-tomcat'] = "Tomcat Service";
	$TEXT['status-named'] = "Domain Name Service (DNS)";
	$TEXT['status-oci8'] = "PHP extension »OCI8/Oracle«";
	$TEXT['status-oci8-url'] = "http://www.apachefriends.org/faq-lampp-en.html#oci8";


	$TEXT['status-lookfaq'] = "lees de FAQ";
	$TEXT['status-ok'] = "GEACTIVEERD";
	$TEXT['status-nok'] = "GEDEACTIVEERD";

	$TEXT['status-tab1'] = "Component";
	$TEXT['status-tab2'] = "Status";
	$TEXT['status-tab3'] = "Hint";

	// ---------------------------------------------------------------------
	// SECURITY
	// ---------------------------------------------------------------------

	$TEXT['security-head'] = "XAMPP beveiliging";
	$TEXT['security-text1'] = "Deze bladzijde geeft een overzicht van de beveiligingsstatus van uw XAMPP installatie. (Ga verder met lezen na de tabel.)";
	$TEXT['security-text2'] = "De groen aangegeven onderdelen zijn veilig; de rood aangegeven onderdelen zijn absoluut onveilig en de geel gemarkeerde onderdelen konden niet worden gecontroleerd (bijv. omdat de software om dit na te lopen niet actief is).<p>Om de problemen voor mysql, phpmyadmin en de xampp directory op te lossen kunt u eenvoudigweg gebruik maken van: </b><p>=> <a href=\"/security/xamppsecurity.php\"><b>http://localhost/security/xamppsecurity.php</b></a> <= &nbsp;&nbsp;[alleen mogelijk op de localhost]<br>&nbsp;<br>&nbsp;<br>
	Nog enkele belangrijke opmerkingen:<ul>
	<li>Al deze test zijn ALLEEN voor de host op \"localhost\" (127.0.0.1).</li>
	<li><i><b>Voor FileZilla FTP en Mercury Mail moet u alle beveiliging zelf instellen! Sorry. </b></i></li>
	<li>Als uw computer niet online is of wordt geblokkeerd door een firewall zijn uw servers VEILIG voor aanvallen van buitenaf.</li>
	<li>Als servers niet draaien is uw systeem altijd VEILIG!</li></ul>";
	$TEXT['security-text3'] = "<b>Let a.u.b op het volgende:
	Bij een hoger ingestelde veiligheid in XAMPP kunnen sommige voorbeelden niet foutvrij werken. Als u bijv. PHP in \"safe mode\" gebruikt zullen sommige functies van het beveiligingspaneel niet meer werken. Meer beveiliging betekent vaak in de praktijk minder functionaliteit.</b>";
	$TEXT['security-text4'] = "The XAMPP default poorten:";

	$TEXT['security-ok'] = " VEILIG ";
	$TEXT['security-nok'] = "!! NIET VEILIG!!";
	$TEXT['security-noidea'] = " ONBEKEND ";

	$TEXT['security-tab1'] = "Onderdeel";
	$TEXT['security-tab2'] = "Status";

	$TEXT['security-checkapache-nok'] = "Deze XAMPP pagina's zijn voor iedereen toegankelijk via het netwerk";
	$TEXT['security-checkapache-ok'] = "Deze XAMPP pagina's zijn niet langer voor iedereen toegankelijk via het netwerk";
	$TEXT['security-checkapache-text'] = "Elke XAMPP demopagina, zoals degene waarnaar u nu kijkt is voor iedereen toegankelijk via het netwerk. Iedereen die uw IP-adres kent kan deze pagina's zien.";

	$TEXT['security-checkmysqlport-nok'] = "MySQL is toegankelijk via het netwerk";
	$TEXT['security-checkmysqlport-ok'] = "MySQL is niet meer toegankelijk via het netwerk";
	$TEXT['security-checkmysqlport-text'] = "Dit is een mogelijk of tenminste een theoretisch beveiligingslek. Bent u een beveiligingsfanaat dan moet u het netwerkinterface of MySQL uitschakelen.";

	$TEXT['security-checkpmamysqluser-nok'] = "De phpMyAdmin gebruiker 'pma' heeft geen wachtwoord";
	$TEXT['security-checkpmamysqluser-ok'] = "De phpMyAdmin gebruiker 'pma'heeft nu een wachtwoord";
	$TEXT['security-checkpmamysqluser-text'] = "phpMyAdmin slaat uw voorkeuren in een extra MySQL database op. Om toegang te krijgen tot deze gegevens maakt phpMyAdmin gebruik van de speciale gebruiker 'pma'. Deze gebruiker heeft bij de standaardinstallatie geen wachtwoord meegekregen; om veiligheidsredenen moet u hem een wachtwoord geven!";

	$TEXT['security-checkmysql-nok'] = "De MySQL-admin gebruiker 'root'heeft GEEN wachtwoord";
	$TEXT['security-checkmysql-ok'] = "De MySQL-admin gebruiker 'root' heeft nu een wachtwoord";
	$TEXT['security-checkmysql-out'] = "A MySQL server is not running  or is blocked by a firewall!";
	$TEXT['security-checkmysql-text'] = "Elke lokale gebruiker met administratorrechten van uw computer heeft toegang tot uw MySQL database. Het is zeer aan te raden een wachtwoord in te stellen!.";

	$TEXT['security-pop-nok'] = "De testgebruiker ('newuser') voor de Mercury Mail server (POP3) heeft een oud wachtwoord ('wampp')";
	$TEXT['security-pop-ok'] = "De testgebruiker \"newuser\" voor de POP3 server (Mercury Mail?) bestaat niet langer of heeft een nieuw wachtwoord";
	$TEXT['security-pop-out'] = "Een POP3 server zoals Mercury Mail draait niet of wordt geblokkeerd door een firewall!";
	$TEXT['security-pop-notload'] = "<i>De benodigde IMAP-extensie voor deze veiligheidstest wordt niet geladen (php.ini)!</i><br>";
	$TEXT['security-pop-text'] = "Controleer dit a.u.b en pas zonodig alle gebruikers en wachtwoorden in de Mercury Mail server configuratie aan!";

	$TEXT['security-checkftppassword-nok'] = "Het wachtwoord voor FileZilla FTP is nog steeds 'wampp'";
	$TEXT['security-checkftppassword-ok'] = "Het wachtwoord voor FileZilla FTP werd gewijzigd";
	$TEXT['security-checkftppassword-out'] = "Er is geen FTP server actief of deze wordt geblokkeerd door een firewall!";
	$TEXT['security-checkftppassword-text'] = "Als de FileZilla FTP server werd opgestart dan kan de standaardgebruiker 'newuser' met wachtwoord 'wampp' bestanden uploaden en wijzigen op uw XAMPP webserver. Dus als u de FileZilla FTP server start moet u een nieuw wachtwoord voor gebruiker 'newuser'instellen.";

	$TEXT['security-phpmyadmin-nok'] = "PhpMyAdmin is vrij toegankelijk via het netwerk";
	$TEXT['security-phpmyadmin-ok'] = "PhpMyAdmin login met wachtwoord is ingeschakeld.";
	$TEXT['security-phpmyadmin-out'] = "PhpMyAdmin: Kan het bestand 'config.inc.php' niet vinden.";
	$TEXT['security-phpmyadmin-text'] = "PhpMyAdmin is via het netwerk toegankelijk zonder wachtwoord. De instelling 'httpd' of 'cookie' in bestand \"config.inc.php\" biedt uitkomst.";

	$TEXT['security-checkphp-nok'] = "PHP draait NIET in \"safe mode\"";
	$TEXT['security-checkphp-ok'] = "PHP draait WEL in \"safe mode\"";
	$TEXT['security-checkphp-out'] = "Kan instellingen voor PHP niet benaderen!";
	$TEXT['security-checkphp-text'] = "Wilt u het voor derden mogelijk maken dat zij PHP toepassingen mogen uitvoeren, denk er dan a.u.b. aan om onder een \"safe mode\" configuratie te werken. Voor standalone werken en voor ontwikkelaars raden wij de \"safe mode\" configuratie NIET aan omdat diverse belangrijke functies dan niet zullen werken. <a href=\"http://www.php.net/features.safe-mode\" target=\"_new\"><font size=1>Klik hier voor meer Info....</font></a>";

	// ---------------------------------------------------------------------
	// SECURITY SETUP
	// ---------------------------------------------------------------------

	$TEXT['mysql-security-head'] = "Beveiligingsscherm van MySQL & XAMPP directory-bescherming";
	$TEXT['mysql-rootsetup-head'] = "MYSQL SECTIE: \"ROOT\" WACHTWOORD";
	$TEXT['mysql-rootsetup-text1'] = "";
	$TEXT['mysql-rootsetup-notrunning'] = "De MySQL server draait niet of wordt geblokkeerd door een firewall! Zorg eerst dat dit probleem wordt opgelost ...";
	$TEXT['mysql-rootsetup-passwdnotok'] = "Het nieuwe wachtwoord is gelijk aan het vorige. Typ a.u.b. beide wachtwoorden opnieuw in!";
	$TEXT['mysql-rootsetup-passwdnull'] = "Lege wachtwoorden ('') worden niet geaccepteerd!";
	$TEXT['mysql-rootsetup-passwdsuccess'] = "GELUKT: Het wachtwoord voor de SuperUser 'root' is ingesteld of vernieuwd!
	LET OP: Het opgeven van een nieuw wachtwoord voor \"root\" betekent wel: MYSQL OPNIEUW OPSTARTEN!!!! Het nieuwe wachtwoord werd opgeslagen in het bestand:";
	$TEXT['mysql-rootsetup-passwdnosuccess'] = "FOUT: Het wachtwoord voor 'root'is misschien verkeerd. MySQL weigert een login met dit huidige wachtwoord voor 'root'.";
	$TEXT['mysql-rootsetup-passwdold'] = "Huidig wachtwoord:";
	$TEXT['mysql-rootsetup-passwd'] = "Nieuw wachtwoord:";
	$TEXT['mysql-rootsetup-passwdrepeat'] = "Herhaal het nieuwe wachtwoord:";
	$TEXT['mysql-rootsetup-passwdchange'] = "Wachtwoord wordt gewijzigd";
	$TEXT['mysql-rootsetup-phpmyadmin'] = "PhpMyAdmin authentificatie:";

	$TEXT['xampp-setup-head'] = "XAMPP DIRECTORY BEVEILIGING (.htaccess)";
	$TEXT['xampp-setup-user'] = "Gebruiker:";
	$TEXT['xampp-setup-passwd'] = "Wachtwoord:";
	$TEXT['xampp-setup-start'] = "Beveilig de volgende XAMPP directory:";
	$TEXT['xampp-setup-notok'] = "<br><br>FOUT: Gebruikersnaam en/of wachtwoord moeten uit tenminste drie en niet meer dan 15 karakters bestaan. Speciale karakters zoals <öäü (enz.) en spaties zijn niet toegestaan!<br><br>";
	$TEXT['xampp-config-ok'] = "<br><br>GELUKT: De XAMPP directory is nu beveiligd! Alle persoonlijke gegevens zijn opgeslagen in het volgende bestand:<br>";
	$TEXT['xampp-setup-ok'] = "<br><br>The root password was successfully changed. Please restart MYSQL for loading these changes!<br><br>";
	$TEXT['xampp-config-notok'] = "<br><br>FOUT: Het systeem kon de directory-beveiliging NIET activeren met \".htaccess\" en \"htpasswd.exe\". Mogelijk draait PHP in de \"Safe Mode\".<br><br>";


	// ---------------------------------------------------------------------
	// START
	// ---------------------------------------------------------------------

	$TEXT['start-head'] = "Welkom bij XAMPP voor Windows";

	$TEXT['start-subhead'] = "Gefeliciteerd!!<br>U hebt XAMPP succesvol op dit systeem geïnstalleerd!";

	$TEXT['start-text-newest'] = "";

	$TEXT['start-text1'] = "U kunt nu beginnen met het gebruiken van Apache en de bijbehorende pakketten. Als eerste kunt u »Status« proberen op de linker navigatie-balk om na te gaan of alles goed werkt.";

	$TEXT['start-text2'] = "Na het testen kunt u de voorbeelden onder de link 'test' bekijken.";

	$TEXT['start-text3'] = "Als u wilt beginnen met programmeren in PHP of Perl (of wat dan ook) kijk dan eerst in <a target=extern href=http://www.apachefriends.org/wampp-en.html>de XAMPP handleiding</a> en leer daar meer over uw XAMPP installatie.";

	$TEXT['start-text4'] = "Ook OpenSSL wordt ondersteund. Gebruik a.u.b. het Testcertificaat op URL <a href='https://127.0.0.1' target='_top'>https://127.0.0.1</a> of op <a href='https://localhost' target='_top'>https://localhost</a>";

	$TEXT['start-text5'] = "Veel succes,<br>Kay Vogelgesang + Kai 'Oswald' Seidler";

	$TEXT['start-text6'] = "";

	// ---------------------------------------------------------------------
	// MANUALS
	// ---------------------------------------------------------------------

	$TEXT['manuals-head'] = "Online documentatie";

	$TEXT['manuals-text1'] = "XAMPP combineert veel verschillende sofwarepakketten in één. Hier is een lijst van de standaard- en referentiedocumentatie van de meest belangrijke pakketten.";


	$TEXT['manuals-list1'] = "
	<ul>
	<li><a href=\"http://httpd.apache.org/docs/2.2/en/\">Apache 2 documentatie</a>
	<li><a href=\"http://www.php.net/manual/en/\">PHP <b>referenz </b>documentatie</a>
	<li><a href=\"http://perldoc.perl.org/\">Perl documentatie</a>
	<li><a href=\"http://dev.mysql.com/doc/refman/5.0/en/index.html\">MySQL documentatie</a>
	<li><a href=\"http://phplens.com/adodb/\">ADODB</a>
	<li><a href=\"http://eaccelerator.net/DocumentationUk/\">eAccelerator</a>
	<li><a href=\"http://www.fpdf.org/en/doc/index.php\">FPDF Class documentatie</a>
	</ul>";

	$TEXT['manuals-text2'] = "En een lijstje van tutorials en de pagina met de Apache Friends documentatie:";

	$TEXT['manuals-list2'] = "
	<ul>
	<li><a href=\"http://www.apachefriends.org/en/faq-xampp.html\">Apache Friends documentation</a>
	<li><a href=\"http://www.freewebmasterhelp.com/tutorials/php/\">PHP Tutorial</a> by David Gowans
	<li><a href=\"http://www.davesite.com/webstation/html/\">HTML - An Interactive Tutorial For Beginners</a> by Dave Kristula
	<li><a href=\"http://www.comp.leeds.ac.uk/Perl/start.html\">Perl Tutorial</a> by Nik Silver
	</ul>";

	$TEXT['manuals-text3'] = "Succes en veel plezier! :)";

	// ---------------------------------------------------------------------
	// COMPONENTS
	// ---------------------------------------------------------------------

	$TEXT['components-head'] = "XAMPP componenten";

	$TEXT['components-text1'] = "XAMPP combineert veel verschillende sofware pakketten in een. Hier is een overzicht van alle pakketten.";

	$TEXT['components-text2'] = "Heel veel dank aan de ontwikkelaars van deze programma's.";

	$TEXT['components-text3'] = "In de directory <b>\\xampp\licenses</b> vindt u alle licenties en README bestanden van deze programma's.";

	// ---------------------------------------------------------------------
	// CD COLLECTION DEMO
	// ---------------------------------------------------------------------

	$TEXT['cds-head'] = "CD Collectie (Voorbeeld tbv PHP+MySQL+PDF Class)";
	$TEXT['cds-head-fpdf'] = "CD Collectie (Voorbeeld tbv PHP+MySQL+FPDF)";

	$TEXT['cds-text1'] = "Een heel simpel CD programma.";

	$TEXT['cds-text2'] = "CD lijst als <a href='$_SERVER[PHP_SELF]?action=getpdf'>PDF document</a>.";

	$TEXT['cds-error'] = "Kon geen verbinding krijgen met de database!<br>Draait MySQL wel? Of hebt u het wachtwoord veranderd?";
	$TEXT['cds-head1'] = "Mijn CD's";
	$TEXT['cds-attrib1'] = "Artiest";
	$TEXT['cds-attrib2'] = "Titel";
	$TEXT['cds-attrib3'] = "Jaar";
	$TEXT['cds-attrib4'] = "Opdracht";
	$TEXT['cds-sure'] = "Zeker?";
	$TEXT['cds-head2'] = "Voeg CD toe";
	$TEXT['cds-button1'] = "Verwijder CD";
	$TEXT['cds-button2'] = "Voeg toe CD";

	// ---------------------------------------------------------------------
	// BIORHYTHM DEMO
	// ---------------------------------------------------------------------

	$TEXT['bio-head'] = "Bioritme (Voorbeeld tbv PHP+GD)";

	$TEXT['bio-by'] = "door";
	$TEXT['bio-ask'] = "Vul de geboortedatum in";
	$TEXT['bio-ok'] = "OK";
	$TEXT['bio-error1'] = "Datum";
	$TEXT['bio-error2'] = "is ongeldig";

	$TEXT['bio-birthday'] = "Geboortedatum";
	$TEXT['bio-today'] = "Vandaag";
	$TEXT['bio-intellectual'] = "Intellectueel";
	$TEXT['bio-emotional'] = "Emotioneel";
	$TEXT['bio-physical'] = "Physiek";

	// ---------------------------------------------------------------------
	// INSTANT ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['iart-head'] = "Instant Art (Voorbeeld tbv PHP+GD+FreeType)";
	$TEXT['iart-text1'] = "Font »AnkeCalligraph« van <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['iart-ok'] = "OK";

	// ---------------------------------------------------------------------
	// FLASH ART DEMO
	// ---------------------------------------------------------------------

	$TEXT['flash-head'] = "Flash Art (Voorbeeld tbv PHP+MING)";
	$TEXT['flash-text1'] = "Font »AnkeCalligraph« door <a class=blue target=extern href=\"http://www.anke-art.de/\">Anke Arnold</a>";
	$TEXT['flash-ok'] = "OK";

	// ---------------------------------------------------------------------
	// PHONE BOOK DEMO
	// ---------------------------------------------------------------------

	$TEXT['phonebook-head'] = "TelefoonBoek (Voorbeeld tbv PHP+SQLite)";

	$TEXT['phonebook-text1'] = "Een erg simpel script voor een telefoonboek. Maar geïmplementeerd met een erg moderne en up-to-date technologie: SQLite, de SQL database zonder server.";

	$TEXT['phonebook-error'] = "Kon de database niet openen!";
	$TEXT['phonebook-head1'] = "Mijn telefoonnummers";
	$TEXT['phonebook-attrib1'] = "Achternaam";
	$TEXT['phonebook-attrib2'] = "Voornaam";
	$TEXT['phonebook-attrib3'] = "Telefoonnummer";
	$TEXT['phonebook-attrib4'] = "Opdracht";
	$TEXT['phonebook-sure'] = "Zeker?";
	$TEXT['phonebook-head2'] = "Voeg entry toe";
	$TEXT['phonebook-button1'] = "Verwijder";
	$TEXT['phonebook-button2'] = "Voeg toe";

	// ---------------------------------------------------------------------
	// ABOUT
	// ---------------------------------------------------------------------

	$TEXT['about-head'] = "Over XAMPP";
	$TEXT['about-subhead1'] = "Idee en realisatie";
	$TEXT['about-subhead2'] = "Ontwerp";
	$TEXT['about-subhead3'] = "Collaboration";
	$TEXT['about-subhead4'] = "Contactpersonen";

	// ---------------------------------------------------------------------
	// MERCURY
	// ---------------------------------------------------------------------

	$TEXT['mail-head'] = "Mailen met Mercury Mail SMTP en een POP3 Server";
	$TEXT['mail-hinweise'] = "Een paar belangrijke zaken over het gebruik van Mercury!";
	$TEXT['mail-adress'] = "Afzender:";
	$TEXT['mail-adressat'] = "Ontvanger:";
	$TEXT['mail-cc'] = "CC:";
	$TEXT['mail-subject'] = "Onderwerp:";
	$TEXT['mail-message'] = "Bericht:";
	$TEXT['mail-sendnow'] = "Dit bericht wordt nu verzonden ...";
	$TEXT['mail-sendok'] = "Het bericht is correct verzonden!";
	$TEXT['mail-sendnotok'] = "FOUT! Verzending van het bericht is mislukt!";
	$TEXT['mail-help1'] = "Opmerkingen bij het gebruik van Mercury:<br><br>";
	$TEXT['mail-help2'] = "<ul>
	<li>Bij het opstarten van Mercury MOET er een externe verbinding aanwezig zijn;</li>
	<li>Bij het opstarten definieert Mercury de Domain Name Service (DNS) automatisch als de Name Server van uw provider;</li>
	<li>Aan alle gebruikers van Gateway Servers: Zet a.u.b. uw DNS via TCP/IP (bijv. via InterNic met IP-nummer 198.41.0.4);</li>
	<li>Het configuratiebestand voor Mercury heet MERCURY.INI;</li>
	<li>TESTEN: zend een bericht naar postmaster@localhost of admin@localhost en controleer op de aanwezigheid van deze berichten in de volgende mappen: xampp.../mailserver/MAIL/postmaster of (...)/admin;</li>
	<li>Er is een TESTgebruiker met gebruikersnaam \"newuser\" (newuser@localhost) en wachtwoord is 'wampp';</li>
	<li>SPAM en obsceniteiten zijn absoluut verboden bij het gebruik van Mercury!;</li>
	</ul>";
	$TEXT['mail-url'] = "<a href=\"http://www.pmail.com/overviews/ovw_mercury.htm\" target=\"_top\">http://www.pmail.com/overviews/ovw_mercury.htm</a>";
	// ---------------------------------------------------------------------
	// FileZilla FTP
	// ---------------------------------------------------------------------

	$TEXT['filezilla-head'] = "FileZilla FTP Server";
	$TEXT['filezilla-install'] = "Apache is <U>geen</U> FTP Server ... daarvoor hebben we vvor u dus FileZilla FTP. Bekijk a.u.b de volgende bestanden.";
	$TEXT['filezilla-install2'] = "In de hoofdmap van XAMPP start u \"filezilla_setup.bat\" voor de installatie.<BR> Let op: Onder Windows NT, 2000 en XP Professional MOET FileZilla als 'service'worden gestart.";
	$TEXT['filezilla-install3'] = "Configuratie van \"FileZilla FTP\". Gebruik hiervoor de FileZilla Interface met \"FileZilla Server Interface.exe\". Er zijn twee gebruikers in dit voorbeeld:<br><br>
	A: Een default gebruiker \"newuser\", wachtwoord \"wampp\". De home directory is xampp\htdocs.<br>
	B: Een anonieme gebruiker \"anonymous\", geen wachtwoord!. De home directory is xampp\anonymous.<br><br>
	De default interface loopt via het loopback adres: http://127.0.0.1.";
	$TEXT['filezilla-install4'] = "De FTP Server wordt gestopt [shutdown] via \"FileZillaFTP_stop.bat\". Voor FileZilla FTP als service dient u \"FileZillaServer.exe\" direct te runnen. U kunt dan alle startopties configureren.";
	$TEXT['filezilla-url'] = "<br><br><a href=\"http://filezilla.sourceforge.net\" target=\"_top\">http://filezilla.sourceforge.net</a>";

	// ---------------------------------------------------------------------
	// PEAR
	// ---------------------------------------------------------------------

	$TEXT['pear-head'] = "Excel exporteren met PEAR (PHP)";
	$TEXT['pear-text'] = "Een kleine <a class=blue target=extern href=\"http://www.contentmanager.de/magazin/artikel_310-print_excel_export_mit_pear.html\">Handleiding</A> door Björn Schotte bij <a class=blue target=extern href=\"http://www.thinkphp.de/\">ThinkPHP</A> (in het Duits)";
	$TEXT['pear-cell'] = "De inhoud van een cel";

	// ---------------------------------------------------------------------
	// JPGRAPH
	// ---------------------------------------------------------------------

	$TEXT['jpgraph-head'] = "JpGraph - Grafische Bibliotheek onder PHP";
	$TEXT['jpgraph-url'] = "<br><br><a href=\"http://www.aditus.nu/jpgraph/\" target=\"_top\">http://www.aditus.nu/jpgraph/</a>";

	// ---------------------------------------------------------------------
	// ADODB
	// ---------------------------------------------------------------------

	$TEXT['ADOdb-head'] = "ADOdb - DataBase toegang (PHP)";
	$TEXT['ADOdb-text'] = "ADOdb komt van Active Data Objects Data Base. Wij ondersteunen momenteel MySQL, PostgreSQL, Interbase, Firebird, Informix, Oracle, MS SQL 7, Foxpro, Access, ADO, Sybase, FrontBase, DB2, SAP DB, SQLite en generic ODBC. De Sybase, Informix, FrontBase en PostgreSQL drivers zijn door de gebruiksgemeenschap aangeleverd. U vindt dit alles in \(mini)xampp\php\pear\adodb.";
	$TEXT['ADOdb-example'] = "Het voorbeeld:";
	$TEXT['ADOdb-dbserver'] = "Database Server (MySQL, Oracle ..?)";
	$TEXT['ADOdb-host'] = "Host van de DB server (naam of IP)";
	$TEXT['ADOdb-user'] = "Gebruikersnaam ";
	$TEXT['ADOdb-password'] = "Wachtwoord";
	$TEXT['ADOdb-database'] = "Naam Database op deze Database Server";
	$TEXT['ADOdb-table'] = "Geselecteerde Table van de database";
	$TEXT['ADOdb-nottable'] = "<p><b>Table niet gevonden!</b>";
	$TEXT['ADOdb-notdbserver'] = "<p><b>De driver voor deze Database Server bestaat niet of is wellicht een ODBC, ADO of OLEDB driver!</b>";


	// ---------------------------------------------------------------------
	// INFO
	// ---------------------------------------------------------------------

	$TEXT['info-package'] = "Pakket";
	$TEXT['info-pages'] = "Pagina's";
	$TEXT['info-extension'] = "Extensies";
	$TEXT['info-module'] = "Apache module";
	$TEXT['info-description'] = "Omschrijving";
	$TEXT['info-signature'] = "Ondertekening";
	$TEXT['info-docdir'] = "Document root";
	$TEXT['info-port'] = "Default poort";
	$TEXT['info-service'] = "Services";
	$TEXT['info-examples'] = "Voorbeelden";
	$TEXT['info-conf'] = "Configuratiebestanden";
	$TEXT['info-requires'] = "Benodigt";
	$TEXT['info-alternative'] = "Alternatief";
	$TEXT['info-tomcatwarn'] = "Waarschuwing! Tomcat werd niet gestart op poort 8080.";
	$TEXT['info-tomcatok'] = "OK! Tomcat gestart op poort 8080.";
	$TEXT['info-tryjava'] = "Java voorbeeld (JSP) met Apache MOD_JK.";
	$TEXT['info-nococoon'] = "Waarschuwing! Tomcat werd niet gestart op poort 8080. Kan \"Cocoon\" niet starten zonder de Tomcat server!";
	$TEXT['info-okcocoon'] = "Ok! De Tomcat Server draait normaal. De installatie duurt misschien een paar minuten! Om \"Cocoon\" te installeren, klik hier ...";

	// ---------------------------------------------------------------------
	// PHP Switch
	// ---------------------------------------------------------------------

	$TEXT['switch-head'] = "PHP Switch 1.0 win32 voor XAMPP";
	$TEXT['switch-phpversion'] = "<i><b>Momenteel draait onder XAMPP de versie: ";
	$TEXT['switch-whatis'] = "<b>Wat doet de PHP switch?</b><br>De Apachefriends PHP Switch voor XAMPP stelt u in staat te wisselen tussen de PHP versie 4 en de versie 5 [dus om en om!!]. U kunt dus uw scripts onder PHP 4 en/of PHP 5 testen.<p>";
	$TEXT['switch-find'] = "<b>Waar vind ik de PHP Switch?</b><br>De PHP Switch voor XAMPP voert een PHP-bestand uit (XAMPP installatie folder) met de naam \"php-switch.php\". Gebruik dit batch bestand voor het wisselen: ";
	$TEXT['switch-care'] = "<b>Waar moet ik op letten?</b><br>De PHP Switch verandert NIET uw PHP versie als a) de Apache HTTPD deamon draait of/en b) het \".phpversion\" bestand in de installatiemap leeg is of fouten bevat. In het \".phpversion\" bestand staat het XAMPP current main PHP versienummer zoiets als \"4\" of \"5\". Dus begin met een \"shutdown\" van de Apache HTTPD deamon en voer DAARNA het bestand \"php-switch.bat\" pas uit.<p>";
	$TEXT['switch-where4'] = "<b>Waar staan daarna mijn (oude) configuratiegegevens?</b><br><br>Voor PHP 4:<br>";
	$TEXT['switch-where5'] = "<br><br>Voor PHP 5:<br>";
	$TEXT['switch-make1'] = "<b>Zijn er dus wijzigingen aangebracht?</b><br><br>JA!! Voor PHP4 of PHP5 in <br>";
	$TEXT['switch-make2'] = "<br><br> .. beveiligd voor PHP4 ...<br>";
	$TEXT['switch-make3'] = "<br><br> .. beveiligd voor PHP5 ...<br>";
	$TEXT['switch-make4'] = "<br><br>En deze bestanden worden teruggezet bij het wijzigen van de PHP-versie met de PHP Switch!!<p>";
	$TEXT['switch-not'] = "<b>Mijn PHP versie vind ik prima EN ik wil GEEN \"PHP Switch\" !!!</b><br>Prima! Vergeet gewoon bovenstaand verhaal ... ;-)<br>";

	// ---------------------------------------------------------------------
	// Cocoon
	// ---------------------------------------------------------------------

	$TEXT['go-cocoon'] = "Cocoon! nu via http://localhost/cocoon/";
	$TEXT['path-cocoon'] = "De juiste mapop uw computer is: ...\\xampp\\tomcat\\webapps\\cocoon";

	// ---------------------------------------------------------------------
	// Guest
	// ---------------------------------------------------------------------

	$TEXT['guest1-name'] = "Current Guest in deze release: <i>FCKeditor</i>";
	$TEXT['guest1-text1'] = "een heel aardige HMTL ONLINE editor met veel JavaScript. Werkt optimaal onder de IE. Werkt NIET onder Mozilla FireFox.";
	$TEXT['guest1-text2'] = "FCKeditor Homepage: <a href=\"http://www.fckeditor.net\" target=\"_new\">www.fckeditor.net</a>. OPM: Het Arial font kan hierbij niet worden gebruikt! Ik weet niet waarom niet! Wie wel?";
	$TEXT['guest1-text3'] = "<a href=\"guest-FCKeditor/fckedit-dynpage.php\" target=\"_new\">Voorbeeldpagina geschreven met de FCKeditor.</A>";
	
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
